<?php
/**

 * PATTERN: Used by ServiceFactory::make('fine') — Factory Pattern.

 */

require_once BASE_PATH . '/models/Fine.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/Reservation.php';
require_once BASE_PATH . '/models/AuditLog.php';
require_once BASE_PATH . '/services/PaymentGatewayService.php';
require_once BASE_PATH . '/helpers/notification_helper.php';

class FineService
{
    private Fine                  $fineModel;
    private User                  $userModel;
    private AuditLog              $auditLog;
    private PaymentGatewayService $gateway;

    public function __construct()
    {
        $this->fineModel = new Fine();
        $this->userModel = new User();
        $this->auditLog  = new AuditLog();
        // Direct instantiation is intentional here — avoids circular ServiceFactory dependency
        $this->gateway   = new PaymentGatewayService();
    }

    // ── Issue Fine ─────────────────────────────────────────────────────────────

    /**
     * ISSUE FINE — Admin or Officer manually generates a fine.
     *
     * Steps:
     * 1. Validate driver exists.
     * 2. Validate amount > 0 and reason not empty.
     * 3. Insert fine with status = 'unpaid'.
     * 4. Notify driver.
     * 5. Write audit log.
     * 6. Check auto-blacklist threshold.
     *
     * @param int      $userId         Driver being fined
     * @param float    $amount         Fine amount in USD
     * @param string   $reason         Description of violation
     * @param int      $issuedBy       Admin/Officer user ID (0 = system)
     * @param int|null $reservationId  Optional linked reservation
     * @return array   ['success' => bool, 'fine_id' => int]
     */
    public function issueFine(
        int    $userId,
        float  $amount,
        string $reason,
        int    $issuedBy,
        ?int   $reservationId = null
    ): array {
        $user = $this->userModel->findById($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'Driver not found.'];
        }

        if ($amount <= 0) {
            return ['success' => false, 'message' => 'Fine amount must be greater than zero.'];
        }

        if (empty(trim($reason))) {
            return ['success' => false, 'message' => 'Fine reason is required.'];
        }

        $fineId = $this->fineModel->create([
            'user_id'        => $userId,
            'reservation_id' => $reservationId,
            'issued_by'      => $issuedBy ?: null,
            'amount'         => $amount,
            'reason'         => $reason,
            'status'         => 'unpaid',
        ]);

        create_notification(
            $userId,
            'Fine Issued',
            "A fine of \${$amount} has been issued: {$reason}. Please pay or appeal within 14 days.",
            'fine'
        );

        $this->auditLog->log(
            $issuedBy ?: null,
            'fine_issued',
            "Fine #{$fineId} issued to user #{$userId} for \${$amount}: {$reason}"
        );

        $this->checkAndBlacklist($userId);

        return ['success' => true, 'fine_id' => $fineId];
    }

    // ── Auto-generate Overstay Fine ────────────────────────────────────────────

    /**
     * GENERATE AUTOMATIC FINE — System-generated overstay fine.
     *
     * Called after grace period expires and driver hasn't checked out.
     *
     * @param int $reservationId
     * @param int $overstayMinutes  Minutes past scheduled end time
     * @return array
     */
    public function generateAutomaticFine(int $reservationId, int $overstayMinutes): array
    {
        $db   = Database::getInstance(); // PATTERN: Singleton
        $stmt = $db->prepare(
            "SELECT r.*, ps.price_per_hour, r.user_id AS driver_id
             FROM reservations r
             JOIN parking_spots ps ON ps.id = r.spot_id
             WHERE r.id = ? AND r.status = 'active'"
        );
        $stmt->execute([$reservationId]);
        $res = $stmt->fetch();

        if (!$res) {
            return ['success' => false, 'message' => 'Active reservation not found.'];
        }

        $overstayHours = $overstayMinutes / 60;
        $fineAmount    = round($overstayHours * (float)$res['price_per_hour'] * OVERSTAY_RATE, 2);
        $reason        = "Overstay: {$overstayMinutes} minutes past scheduled checkout.";

        return $this->issueFine(
            (int)$res['driver_id'],
            $fineAmount,
            $reason,
            0,
            $reservationId
        );
    }

    // ── Pay Fine ───────────────────────────────────────────────────────────────

    /**
     * PAY FINE — Driver pays an outstanding fine.
     *
     * Payment lifecycle:
     *   1. chargeFine() via gateway → payment created as 'completed' immediately
     *   2. Mark fine as 'paid'
     *   3. Check if user can be reinstated (if they were blacklisted)
     *
     * @param int    $fineId
     * @param int    $userId   Must match fine's user_id (security check)
     * @param string $method   Payment method string
     * @return array           ['success', 'transaction_ref']
     */
    public function payFine(int $fineId, int $userId, string $method = 'credit_card'): array
    {
        $fine = $this->fineModel->findById($fineId);

        if (!$fine) {
            return ['success' => false, 'message' => 'Fine not found.'];
        }
        if ((int)$fine['user_id'] !== $userId) {
            return ['success' => false, 'message' => 'This fine does not belong to you.'];
        }
        if ($fine['status'] !== 'unpaid') {
            return ['success' => false, 'message' => 'Fine is already ' . $fine['status'] . '.'];
        }

        $result = $this->gateway->chargeFine($fineId, $userId, (float)$fine['amount'], $method);

        if ($result['success']) {
            $this->fineModel->markPaid($fineId);

            // Reinstate user if they dropped below the blacklist threshold
            $remainingUnpaid = $this->fineModel->countUnpaid($userId);
            if ($remainingUnpaid < MAX_UNPAID_FINES) {
                $user = $this->userModel->findById($userId);
                if ($user && $user['status'] === 'blacklisted') {
                    $this->userModel->update($userId, ['status' => 'active']);
                    create_notification(
                        $userId,
                        'Account Reinstated',
                        'Your account has been reinstated after paying your fines.',
                        'system'
                    );
                }
            }
        }

        return $result;
    }

    // ── Waive Fine ────────────────────────────────────────────────────────────

    /**
     * WAIVE FINE — Admin forgives a fine.
     *
     * @param int    $fineId
     * @param int    $adminId
     * @param string $reason
     * @return bool
     */
    public function waiveFine(int $fineId, int $adminId, string $reason = ''): bool
    {
        $fine = $this->fineModel->findById($fineId);
        if (!$fine) return false;

        $this->fineModel->waive($fineId);

        create_notification(
            (int)$fine['user_id'],
            'Fine Waived',
            'Your fine has been waived' . ($reason ? ": {$reason}" : '.'),
            'fine'
        );

        $this->auditLog->log(
            $adminId,
            'fine_waived',
            "Fine #{$fineId} waived by admin #{$adminId}. Reason: {$reason}"
        );

        return true;
    }

    // ── Stats ─────────────────────────────────────────────────────────────────

    /**
     * GET FINE STATS — for admin dashboard widget.
     *
     * FIX: Delegates to Fine::getStats() instead of running raw queries inline.
     * This is the method called by AdminController::dashboard() and
     * FineController::adminFines().
     *
     * @return array  ['total', 'unpaid', 'paid', 'appealed', 'waived', 'revenue']
     */
    public function getFineStats(): array
    {
        return $this->fineModel->getStats();
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    /**
     * Auto-blacklist driver if unpaid fines >= MAX_UNPAID_FINES threshold.
     *
     * @param int $userId
     */
    private function checkAndBlacklist(int $userId): void
    {
        $unpaidCount = $this->fineModel->countUnpaid($userId);

        if ($unpaidCount >= MAX_UNPAID_FINES) {
            $user = $this->userModel->findById($userId);

            if ($user && $user['status'] !== 'blacklisted') {
                $this->userModel->update($userId, ['status' => 'blacklisted']);

                create_notification(
                    $userId,
                    'Account Blacklisted',
                    "Your account has been blacklisted due to {$unpaidCount} unpaid fines. "
                    . "Pay all outstanding fines to reinstate your account.",
                    'system'
                );

                $this->auditLog->log(
                    null,
                    'user_blacklisted',
                    "User #{$userId} auto-blacklisted. Unpaid fines: {$unpaidCount}"
                );
            }
        }
    }
}