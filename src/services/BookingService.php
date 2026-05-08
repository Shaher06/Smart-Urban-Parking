<?php
/**
 * BOOKING SERVICE — Refactored
 *
 * KEY CHANGE: Now uses PricingContext (Strategy Pattern) to calculate price.
 * Previously: inline price calculation.
 * Now: delegates to the correct PricingStrategy based on time + zone.
 *
 * Also integrated with:
 * - PaymentGatewayService (correct lifecycle)
 * - OccupancyService (nearby alternatives on full spot)
 * - AuditLog
 */

require_once BASE_PATH . '/models/Reservation.php';
require_once BASE_PATH . '/models/ParkingSpot.php';
require_once BASE_PATH . '/models/PromoCode.php';
require_once BASE_PATH . '/models/AuditLog.php';
require_once BASE_PATH . '/models/EventZone.php';
require_once BASE_PATH . '/services/PricingContext.php';
require_once BASE_PATH . '/services/PaymentGatewayService.php';
require_once BASE_PATH . '/helpers/notification_helper.php';
require_once BASE_PATH . '/config/app.php';

class BookingService
{
    private Reservation $reservationModel;
    private ParkingSpot $spotModel;
    private PromoCode   $promoModel;
    private AuditLog    $auditLog;
    private EventZone   $eventZoneModel;

    public function __construct()
    {
        $this->reservationModel = new Reservation();
        $this->spotModel        = new ParkingSpot();
        $this->promoModel       = new PromoCode();
        $this->auditLog         = new AuditLog();
        $this->eventZoneModel   = new EventZone();
    }

    /**
     * BOOK A SPOT
     *
     * Workflow:
     * 1. Validate spot exists and is active
     * 2. Check available slots
     * 3. Check for booking conflicts + buffer time
     * 4. Detect event zone → select pricing strategy
     * 5. Apply promo code discount
     * 6. Create reservation record
     * 7. Decrement available slots
     * 8. Create notification + audit log
     *
     * PATTERN: Uses PricingContext (Strategy) for price calculation.
     */
    public function book(array $data): array
    {
        // ── 0. Blacklist / suspension guard (Priority 9) ──────────────────────
        $db = Database::getInstance();
        $userStmt = $db->prepare("SELECT status FROM users WHERE id = ? LIMIT 1");
        $userStmt->execute([$data['user_id']]);
        $userStatus = $userStmt->fetchColumn();
        if ($userStatus === 'suspended') {
            return ['success' => false, 'message' => 'Your account is suspended. You cannot make reservations.'];
        }
        if ($userStatus === 'blacklisted') {
            return ['success' => false, 'message' => 'Your account is blacklisted. Bookings are not allowed. Please contact support.'];
        }

        // ── 1. Validate spot ──────────────────────────────────────────────────
        $spot = $this->spotModel->findById((int)$data['spot_id']);
        if (!$spot || $spot['status'] !== 'active') {
            return ['success' => false, 'message' => 'Parking spot is not available.'];
        }

        // ── 2. Check available slots ─────────────────────────────────────────
        if ((int)$spot['available_slots'] <= 0) {
            // SRS: Nearby Alternative Suggestion
            $alternatives = $this->spotModel->getNearbyAlternatives(
                (int)$spot['id'], $spot['city']
            );
            return [
                'success'      => false,
                'message'      => 'No available slots at this spot.',
                'alternatives' => $alternatives,
            ];
        }

        // ── 3. Validate times ─────────────────────────────────────────────────
        $start = $data['start_time'];
        $end   = $data['end_time'];

        if (strtotime($end) <= strtotime($start)) {
            return ['success' => false, 'message' => 'End time must be after start time.'];
        }

        // Buffer time: end_time + BUFFER_MINUTES must not overlap next booking
        $bufferEnd = date('Y-m-d H:i:s', strtotime($end) + (BUFFER_MINUTES * 60));

        if ($this->reservationModel->hasConflict((int)$data['spot_id'], $start, $bufferEnd)) {
            return [
                'success' => false,
                'message' => "Time slot conflicts with an existing booking "
                           . "(including " . BUFFER_MINUTES . "-minute buffer time).",
            ];
        }

        // ── 4. Select pricing strategy ────────────────────────────────────────
        // Check if spot falls inside an active event zone
        $isEventZone = $this->isInEventZone((int)$data['spot_id'], $start);

        // PATTERN: PricingContext.forBooking() selects the right strategy
        $pricingCtx    = PricingContext::forBooking($start, $isEventZone);
        $strategyName  = $pricingCtx->getStrategyName();
        $totalPrice    = $pricingCtx->calculate(
            (float)$spot['price_per_hour'], $start, $end
        );

        if ($totalPrice <= 0) {
            return ['success' => false, 'message' => 'Invalid booking duration.'];
        }

        // ── 5. Apply promo code ───────────────────────────────────────────────
        $promoId      = null;
        $discountApplied = 0;
        if (!empty($data['promo_code'])) {
            $promo = $this->promoModel->findByCode(trim($data['promo_code']));
            if ($promo) {
                $discountApplied = $totalPrice * ((float)$promo['discount_percent'] / 100);
                $totalPrice     -= $discountApplied;
                $totalPrice      = round($totalPrice, 2);
                $promoId         = (int)$promo['id'];
                $this->promoModel->incrementUsage($promo['id']);
            }
        }

        // ── 5b. Loyalty discount (repeat customers) ───────────────────────────
        $loyaltyPct = 0.0;
        if ($totalPrice > 0
            && $this->reservationModel->countCompletedByUser((int)$data['user_id']) >= LOYALTY_MIN_BOOKINGS
        ) {
            $loyaltyPct      = (float) LOYALTY_DISCOUNT_PERCENT;
            $loyaltyAmount   = round($totalPrice * ($loyaltyPct / 100), 2);
            $totalPrice     -= $loyaltyAmount;
            $totalPrice      = max(0.0, round($totalPrice, 2));
            $discountApplied += $loyaltyAmount;
        }

        // ── 6. Create reservation ─────────────────────────────────────────────
        $qrCode = 'QR-' . strtoupper(bin2hex(random_bytes(8)));

        $resId = $this->reservationModel->create([
            'user_id'        => (int)$data['user_id'],
            'spot_id'        => (int)$data['spot_id'],
            'vehicle_id'     => !empty($data['vehicle_id']) ? (int)$data['vehicle_id'] : null,
            'start_time'     => $start,
            'end_time'       => $end,
            'status'         => RES_CONFIRMED,
            'total_price'    => $totalPrice,
            'qr_code'        => $qrCode,
            'promo_code_id'  => $promoId,
        ]);

        // ── 7. Decrement slot ─────────────────────────────────────────────────
        $this->spotModel->decrementSlot((int)$data['spot_id']);

        // ── 8. Notifications & audit log ─────────────────────────────────────
        $msg = "Booking confirmed at {$spot['name']}. "
             . "Pricing: {$strategyName}. Total: \${$totalPrice}. QR: {$qrCode}";

        if ($discountApplied > 0) {
            $msg .= " (Discount: -\$" . number_format($discountApplied, 2) . ')';
        }

        create_notification((int)$data['user_id'], 'Booking Confirmed', $msg, 'booking');

        $this->auditLog->log(
            (int)$data['user_id'],
            'booking_created',
            "Reservation #{$resId} at spot #{$data['spot_id']}. {$strategyName}. \${$totalPrice}"
        );

        return [
            'success'        => true,
            'reservation_id' => $resId,
            'qr_code'        => $qrCode,
            'total_price'    => $totalPrice,
            'strategy'       => $strategyName,
            'discount'       => $discountApplied,
        ];
    }

    /**
     * CANCEL RESERVATION
     *
     * Refund tiering (SRS: Reservation Cancellation Tiering):
     *   > 2 hours before start  → 100% refund
     *   1–2 hours before start  → 50% refund
     *   < 1 hour before start   → 0% refund
     */
    public function cancel(int $reservationId, int $userId): array
    {
        $res = $this->reservationModel->findById($reservationId);

        if (!$res || (int)$res['user_id'] !== $userId) {
            return ['success' => false, 'message' => 'Reservation not found.'];
        }
        if (!in_array($res['status'], [RES_CONFIRMED, RES_PENDING], true)) {
            return ['success' => false, 'message' => 'This reservation cannot be cancelled.'];
        }

        $hoursUntilStart = (strtotime($res['start_time']) - time()) / 3600;

        $refundPct = match(true) {
            $hoursUntilStart >= 2 => 100,
            $hoursUntilStart >= 1 => 50,
            default               => 0,
        };

        $refundAmount = round((float)$res['total_price'] * ($refundPct / 100), 2);

        // Update reservation
        $this->reservationModel->cancel($reservationId, $refundAmount);

        // Return slot
        $this->spotModel->incrementSlot((int)$res['spot_id']);

        // Process refund / update payment status (Priority 6)
        $db   = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT id, transaction_ref, amount FROM payments
             WHERE reservation_id = ? AND status = 'escrow'
             ORDER BY id ASC"
        );
        $stmt->execute([$reservationId]);
        $escrowPays = $stmt->fetchAll();

        if (!empty($escrowPays)) {
            $gateway = new PaymentGatewayService();

            // Refund policy applies to the reservation's current total_price.
            // We allocate the refund across escrow payments in order (id asc).
            $remainingRefund = $refundAmount;
            foreach ($escrowPays as $p) {
                $pid  = (int)$p['id'];
                $pref = (string)$p['transaction_ref'];
                $pamt = (float)$p['amount'];

                if ($refundAmount <= 0) {
                    // No refund — release all escrow to completed (owner keeps funds)
                    $gateway->release($pid);
                    continue;
                }

                if ($remainingRefund <= 0) {
                    // Refund quota satisfied; release remaining escrow
                    $gateway->release($pid);
                    continue;
                }

                $toRefund = min($pamt, $remainingRefund);
                $gateway->refund($pref, $toRefund);
                $remainingRefund = round($remainingRefund - $toRefund, 2);
            }
        }

        create_notification(
            $userId,
            'Reservation Cancelled',
            "Your reservation has been cancelled. Refund: \${$refundAmount} ({$refundPct}%).",
            'booking'
        );

        $this->auditLog->log(
            $userId,
            'booking_cancelled',
            "Reservation #{$reservationId} cancelled. Refund: \${$refundAmount} ({$refundPct}%)"
        );

        return [
            'success'      => true,
            'refund'       => $refundAmount,
            'refund_pct'   => $refundPct,
        ];
    }

    /**
     * EXTEND RESERVATION
     *
     * SRS: Instant Extension Protocol
     * Checks next slot availability (with buffer), extends end time, charges difference.
     */
    public function extend(int $reservationId, int $userId, int $extraHours): array
    {
        if ($extraHours < 1 || $extraHours > 12) {
            return ['success' => false, 'message' => 'Extension must be between 1 and 12 hours.'];
        }

        $res = $this->reservationModel->findById($reservationId);
        if (!$res || (int)$res['user_id'] !== $userId) {
            return ['success' => false, 'message' => 'Reservation not found.'];
        }
        if (!in_array($res['status'], [RES_CONFIRMED, RES_ACTIVE], true)) {
            return ['success' => false, 'message' => 'Cannot extend this reservation.'];
        }

        $newEnd    = date('Y-m-d H:i:s', strtotime($res['end_time']) + ($extraHours * 3600));
        $bufferEnd = date('Y-m-d H:i:s', strtotime($newEnd) + (BUFFER_MINUTES * 60));

        if ($this->reservationModel->hasConflict(
            (int)$res['spot_id'], $res['end_time'], $bufferEnd, $reservationId
        )) {
            return ['success' => false, 'message' => 'Cannot extend — another booking starts soon.'];
        }

        $spot      = $this->spotModel->findById((int)$res['spot_id']);
        $pricingCtx = PricingContext::forBooking($res['end_time'], false);
        $extraCost  = $pricingCtx->calculate((float)$spot['price_per_hour'], $res['end_time'], $newEnd);

        $this->reservationModel->extend($reservationId, $newEnd, $extraCost);

        create_notification(
            $userId,
            'Reservation Extended',
            "Extended by {$extraHours}h. New checkout: {$newEnd}. Extra charge: \${$extraCost}",
            'booking'
        );

        $this->auditLog->log(
            $userId,
            'booking_extended',
            "Reservation #{$reservationId} extended by {$extraHours}h → {$newEnd}. Extra: \${$extraCost}"
        );

        return [
            'success'    => true,
            'new_end'    => $newEnd,
            'extra_cost' => $extraCost,
        ];
    }

    /**
     * Check if a parking spot is inside an active event zone.
     * Used by PricingContext to select EventZonePricingStrategy.
     */
    private function isInEventZone(int $spotId, string $datetime): bool
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT id FROM event_zones
             WHERE status = 'active'
               AND start_time <= ?
               AND end_time   >= ?
               AND FIND_IN_SET(?, REPLACE(affected_spot_ids, ' ', '')) > 0
             LIMIT 1"
        );
        $stmt->execute([$datetime, $datetime, $spotId]);
        return (bool)$stmt->fetchColumn();
    }
}