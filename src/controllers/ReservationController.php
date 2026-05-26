<?php


require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/core/ServiceFactory.php';
require_once BASE_PATH . '/models/Reservation.php';
require_once BASE_PATH . '/models/ParkingSpot.php';
require_once BASE_PATH . '/models/Vehicle.php';

class ReservationController extends Controller
{
    private Reservation $reservationModel;
    private ParkingSpot $spotModel;
    private Vehicle     $vehicleModel;

    public function __construct()
    {
        parent::__construct();
        $this->reservationModel = new Reservation();
        $this->spotModel        = new ParkingSpot();
        $this->vehicleModel     = new Vehicle();
    }

    public function bookSpot(): void
    {
        $this->requireRole('driver');
        $spotId = (int)$this->get('id');
        $spot   = $this->spotModel->findById($spotId);

        if (!$spot || $spot['status'] !== 'active') {
            set_flash('error', 'Spot not available.');
            $this->redirect('?page=browse-spots');
            return;
        }

        if ($this->isPost()) {
            /** @var BookingService $bookingService */
            $bookingService = ServiceFactory::make('booking'); // PATTERN: Factory
            $postVehicle = $this->post('vehicle_id');
            $defaultV    = isset(current_user()['default_vehicle_id'])
                ? (int) current_user()['default_vehicle_id'] : 0;
            $vehicleId   = ($postVehicle !== '' && $postVehicle !== null)
                ? (int) $postVehicle
                : ($defaultV > 0 ? $defaultV : null);

            $result = $bookingService->book([
                'user_id'    => current_user_id(),
                'spot_id'    => $spotId,
                'vehicle_id' => $vehicleId,
                'start_time' => $this->post('start_time'),
                'end_time'   => $this->post('end_time'),
                'promo_code' => $this->post('promo_code'),
            ]);

            if (!$result['success']) {
                set_flash('error', $result['message']);

                // Show nearby alternatives if spot is full
                $vehicles = $this->vehicleModel->getByUser(current_user_id());
                $this->render('driver/book-spot', [
                    'spot'               => $spot,
                    'vehicles'           => $vehicles,
                    'alternatives'       => $result['alternatives'] ?? [],
                    'default_vehicle_id' => (int) (current_user()['default_vehicle_id'] ?? 0),
                ]);
                return;
            }

            // PAYMENT LIFECYCLE: Step 1 — Charge → escrow
            /** @var PaymentGatewayService $gateway */
            $gateway = ServiceFactory::make('payment');
            $payResult = $gateway->charge($result['total_price'], [
                'user_id'        => current_user_id(),
                'reservation_id' => $result['reservation_id'],
                'method'         => $this->post('payment_method', 'credit_card'),
            ]);

            if (!$payResult['success']) {
                // Payment failed — cancel the reservation automatically
                $bookingService->cancel($result['reservation_id'], current_user_id());
                set_flash('error', 'Payment failed. Reservation has been cancelled.');
                $this->redirect('?page=browse-spots');
                return;
            }

            set_flash('success',
                "Booking confirmed! QR: {$result['qr_code']}. "
                . "Pricing: {$result['strategy']}. "
                . "Total: \${$result['total_price']}"
                . ($result['discount'] > 0 ? " (Saved: \${$result['discount']})" : '')
            );
            $this->redirect('?page=reservations');
            return;
        }

        $vehicles = $this->vehicleModel->getByUser(current_user_id());
        $this->render('driver/book-spot', [
            'spot'                 => $spot,
            'vehicles'             => $vehicles,
            'alternatives'         => [],
            'default_vehicle_id'   => (int) (current_user()['default_vehicle_id'] ?? 0),
        ]);
    }

    public function reservations(): void
    {
        $this->requireRole('driver');
        $reservations = $this->reservationModel->getByUser(current_user_id());
        $this->render('driver/reservations', ['reservations' => $reservations]);
    }

    public function reservationHistory(): void
    {
        $this->requireRole('driver');
        $reservations = $this->reservationModel->getByUser(current_user_id());
        $this->render('driver/reservation-history', ['reservations' => $reservations]);
    }

    public function cancelReservation(): void
    {
        $this->requireRole('driver');
        $id = (int)$this->get('id');

        $bookingService = ServiceFactory::make('booking');
        // Get spot_id before cancel for waitlist notification
        $resBefore = $this->reservationModel->findById($id);
        $result    = $bookingService->cancel($id, current_user_id());

        if ($result['success']) {
            // Notify waitlist users that a slot opened (Priority 9)
            if ($resBefore) {
                notify_waitlist_for_spot((int)$resBefore['spot_id']);
            }
            set_flash('success',
                "Reservation cancelled. Refund: \${$result['refund']} ({$result['refund_pct']}%)"
            );
        } else {
            set_flash('error', $result['message']);
        }

        $this->redirect('?page=reservations');
    }

    public function extendReservation(): void
    {
        $this->requireRole('driver');
        $id  = (int)$this->get('id');
        $res = $this->reservationModel->findById($id);

        if (!$res || (int)$res['user_id'] !== current_user_id()) {
            set_flash('error', 'Reservation not found.');
            $this->redirect('?page=reservations');
            return;
        }

        if ($this->isPost()) {
            $hours  = max(1, min(12, (int)$this->post('extra_hours', 1)));
            $result = ServiceFactory::make('booking')->extend($id, current_user_id(), $hours);

            if ($result['success']) {
                // Charge for extension
                $gateway = ServiceFactory::make('payment');
                $gateway->charge($result['extra_cost'], [
                    'user_id'        => current_user_id(),
                    'reservation_id' => $id,
                    'method'         => 'credit_card',
                ]);

                set_flash('success',
                    "Extended until {$result['new_end']}. Extra: \${$result['extra_cost']}"
                );
            } else {
                set_flash('error', $result['message']);
            }

            $this->redirect('?page=reservations');
            return;
        }

        $reservations = $this->reservationModel->getByUser(current_user_id());
        $this->render('driver/reservations', [
            'reservations' => $reservations,
            'extend_id'    => $id,
        ]);
    }

    public function checkInOut(): void
    {
        $this->requireRole('driver');

        if ($this->isPost()) {
            $id     = (int)$this->post('reservation_id');
            $action = $this->post('action');
            $res    = $this->reservationModel->findById($id);

            if (!$res || (int)$res['user_id'] !== current_user_id()) {
                set_flash('error', 'Invalid reservation.');
                $this->redirect('?page=check-in-out');
                return;
            }

            if ($action === 'checkin' && $res['status'] === RES_CONFIRMED) {
                $this->reservationModel->checkIn($id);
                set_flash('success', 'Checked in! Enjoy your parking.');

            } elseif ($action === 'checkout' && $res['status'] === RES_ACTIVE) {
                $this->reservationModel->checkOut($id);
                $this->spotModel->incrementSlot((int)$res['spot_id']);
                // Notify waitlist users that a slot opened (Priority 9)
                notify_waitlist_for_spot((int)$res['spot_id']);

                // PAYMENT LIFECYCLE: Step 2 — Release escrow → completed
                $db   = Database::getInstance();
                $stmt = $db->prepare(
                    "SELECT id FROM payments WHERE reservation_id = ? AND status = 'escrow'"
                );
                $stmt->execute([$id]);
                $paymentIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);

                if (!empty($paymentIds)) {
                    $gateway = ServiceFactory::make('payment');
                    foreach ($paymentIds as $paymentId) {
                        $gateway->release((int)$paymentId);
                    }
                }

                // PRIORITY 4 — Overstay penalty
                $overstayMsg = '';
                $now         = time();
                $endTime     = strtotime($res['end_time']);
                if ($now > $endTime + (GRACE_MINUTES * 60)) {
                    $overstayMinutes = (int)(($now - $endTime) / 60);
                    $spot            = $this->spotModel->findById((int)$res['spot_id']);
                    require_once BASE_PATH . '/services/PricingService.php';
                    $pricingSvc = new PricingService();
                    $penalty    = $pricingSvc->calculateOverstayPenalty(
                        (float)$spot['price_per_hour'], $overstayMinutes
                    );

                    if ($penalty > 0) {
                        // Issue a fine record
                        require_once BASE_PATH . '/models/Fine.php';
                        $fineModel = new Fine();
                        $fineId    = $fineModel->create([
                            'user_id'        => current_user_id(),
                            'reservation_id' => $id,
                            'issued_by'      => null,
                            'amount'         => $penalty,
                            'reason'         => "Overstay penalty: {$overstayMinutes} minutes beyond reservation end.",
                            'status'         => FINE_UNPAID,
                        ]);

                        // Audit log
                        require_once BASE_PATH . '/models/AuditLog.php';
                        (new AuditLog())->log(current_user_id(), 'overstay_fine_issued',
                            "Fine #{$fineId} issued. Overstay {$overstayMinutes}min. Penalty \${$penalty} on reservation #{$id}");

                        // Notify driver
                        create_notification(
                            current_user_id(),
                            'Overstay Penalty',
                            "You overstayed by {$overstayMinutes} minutes. An overstay fine of \${$penalty} has been issued.",
                            'payment'
                        );

                        $overstayMsg = " ⚠️ Overstay penalty of \${$penalty} issued for {$overstayMinutes} minutes. Check your fines.";
                    }
                }

                set_flash('success', 'Checked out successfully! Funds released. Thank you.' . $overstayMsg);

            } else {
                set_flash('error', 'Invalid action for current reservation status.');
            }

            $this->redirect('?page=check-in-out');
            return;
        }

        $reservations = $this->reservationModel->getByUser(current_user_id());
        $this->render('driver/check-in-out', ['reservations' => $reservations]);
    }

    public function navigate(): void
    {
        $this->requireRole('driver');
        $id  = (int)$this->get('id');
        $res = $this->reservationModel->getDetailedById($id);

        if (!$res || (int)$res['user_id'] !== current_user_id()) {
            set_flash('error', 'Reservation not found.');
            $this->redirect('?page=reservations');
            return;
        }

        $this->render('driver/navigate', ['reservation' => $res]);
    }
}