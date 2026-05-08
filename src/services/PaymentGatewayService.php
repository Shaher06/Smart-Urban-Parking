<?php
/**
 * PAYMENT GATEWAY SERVICE — Refactored
 *
 * PATTERN: Strategy (implements PaymentService interface)
 * PATTERN: Uses Singleton Database via Database::getInstance()
 *
 * Payment Lifecycle (fixed):
 *   1. charge()  → creates payment with status 'escrow' (funds locked)
 *   2. release() → moves payment from 'escrow' to 'completed' (after checkout)
 *   3. refund()  → marks payment as 'refunded', logs it
 *   4. fail()    → marks payment as 'failed'
 *
 * FIX: Previously the lifecycle was unclear — escrow and completed were mixed.
 *      Now: pending → escrow → completed, or pending → failed, or completed → refunded.
 */

require_once BASE_PATH . '/interfaces/PaymentService.php';
require_once BASE_PATH . '/models/Payment.php';
require_once BASE_PATH . '/models/AuditLog.php';
require_once BASE_PATH . '/helpers/notification_helper.php';

class PaymentGatewayService implements PaymentService
{
    private Payment  $paymentModel;
    private AuditLog $auditLog;

    public function __construct()
    {
        $this->paymentModel = new Payment();
        $this->auditLog     = new AuditLog();
    }

    /**
     * CHARGE — Simulates charging the user.
     *
     * Payment lifecycle step 1: creates record with status 'escrow'.
     * Funds are locked until the reservation is completed.
     *
     * @param float  $amount
     * @param array  $details  ['user_id', 'reservation_id'?, 'fine_id'?, 'method']
     * @return array           ['success', 'payment_id', 'transaction_ref', 'status']
     */
    public function charge(float $amount, array $details): array
    {
        if ($amount <= 0) {
            return ['success' => false, 'message' => 'Invalid payment amount.'];
        }

        // Simulate gateway call (in production: call Stripe/PayPal API here)
        $transactionRef = 'TXN-' . strtoupper(bin2hex(random_bytes(6)));
        $gatewaySuccess = true; // Simulate 100% success rate

        if (!$gatewaySuccess) {
            return ['success' => false, 'message' => 'Payment gateway declined.'];
        }

        // Step 1: Insert with status = 'escrow' (funds locked, not yet released to owner)
        $paymentId = $this->paymentModel->create([
            'user_id'         => $details['user_id'],
            'reservation_id'  => $details['reservation_id'] ?? null,
            'fine_id'         => $details['fine_id'] ?? null,
            'amount'          => $amount,
            'method'          => $details['method'] ?? 'credit_card',
            'status'          => PAY_ESCROW,
            'transaction_ref' => $transactionRef,
            'escrow_locked'   => 1,
        ]);

        // Notify user
        create_notification(
            $details['user_id'],
            'Payment Received',
            'Your payment of $' . number_format($amount, 2) . ' is processing. Ref: ' . $transactionRef,
            'payment'
        );

        // Audit log
        $this->auditLog->log(
            $details['user_id'],
            'payment_charged',
            "Payment #{$paymentId} of \${$amount} locked in escrow. Ref: {$transactionRef}"
        );

        return [
            'success'         => true,
            'payment_id'      => $paymentId,
            'transaction_ref' => $transactionRef,
            'status'          => PAY_ESCROW,
        ];
    }

    /**
     * RELEASE — Move payment from escrow to completed.
     *
     * Payment lifecycle step 2: called after successful checkout.
     * This is when the owner's funds are "released" to their payout balance.
     */
    public function release(int $paymentId): array
    {
        $payment = $this->paymentModel->findById($paymentId);
        if (!$payment) {
            return ['success' => false, 'message' => 'Payment not found.'];
        }
        if ($payment['status'] !== PAY_ESCROW) {
            return ['success' => false, 'message' => 'Payment is not in escrow state.'];
        }

        $this->paymentModel->updateStatus($paymentId, PAY_COMPLETED);
        $this->paymentModel->releaseEscrow($paymentId);

        $this->auditLog->log(
            $payment['user_id'],
            'payment_released',
            "Payment #{$paymentId} released from escrow → completed."
        );

        return ['success' => true, 'status' => PAY_COMPLETED];
    }

    /**
     * REFUND — Simulate refund to the user.
     *
     * Payment lifecycle: completed → refunded
     */
    public function refund(string $transactionRef, float $amount): array
    {
        $db   = Database::getInstance(); // PATTERN: Singleton
        $stmt = $db->prepare("SELECT * FROM payments WHERE transaction_ref = ? LIMIT 1");
        $stmt->execute([$transactionRef]);
        $payment = $stmt->fetch();

        if (!$payment) {
            return ['success' => false, 'message' => 'Transaction not found.'];
        }

        // Idempotency guard: prevent double-refunds
        if ($payment['status'] === PAY_REFUNDED) {
            return ['success' => false, 'message' => 'Payment is already refunded.'];
        }

        // Only allow refund from escrow or completed states
        if (!in_array($payment['status'], [PAY_ESCROW, PAY_COMPLETED], true)) {
            return ['success' => false, 'message' => 'Payment is not refundable in its current state.'];
        }

        $maxAmount = (float)$payment['amount'];
        if ($amount <= 0 || $amount > $maxAmount) {
            $amount = $maxAmount; // safest fallback: refund full recorded amount
        }

        $refundRef = 'REF-' . strtoupper(bin2hex(random_bytes(6)));

        // Conditional update to prevent race double-processing
        $upd = $db->prepare(
            "UPDATE payments
             SET status = 'refunded', escrow_locked = 0, escrow_released = 0
             WHERE transaction_ref = ?
               AND status IN ('escrow','completed')"
        );
        $upd->execute([$transactionRef]);

        if ($upd->rowCount() !== 1) {
            return ['success' => false, 'message' => 'Refund could not be processed (already handled).'];
        }

        create_notification(
            $payment['user_id'],
            'Refund Processed',
            "Refund of \${$amount} processed. Ref: {$refundRef}",
            'payment'
        );

        $this->auditLog->log(
            $payment['user_id'],
            'payment_refunded',
            "Payment {$transactionRef} refunded \${$amount}. Refund ref: {$refundRef}"
        );

        return [
            'success'    => true,
            'refund_ref' => $refundRef,
            'amount'     => $amount,
            'status'     => PAY_REFUNDED,
        ];
    }

    /**
     * Get current status of a payment by transaction reference.
     */
    public function getStatus(string $transactionRef): string
    {
        $stmt = Database::getInstance()->prepare(
            "SELECT status FROM payments WHERE transaction_ref = ? LIMIT 1"
        );
        $stmt->execute([$transactionRef]);
        return $stmt->fetchColumn() ?: 'unknown';
    }

    /**
     * Direct fine payment (skips escrow — fine payment goes straight to completed).
     */
    public function chargeFine(int $fineId, int $userId, float $amount, string $method): array
    {
        $transactionRef = 'FINE-' . strtoupper(bin2hex(random_bytes(6)));

        $paymentId = $this->paymentModel->create([
            'user_id'         => $userId,
            'reservation_id'  => null,
            'fine_id'         => $fineId,
            'amount'          => $amount,
            'method'          => $method,
            'status'          => PAY_COMPLETED, // Fine payments complete immediately
            'transaction_ref' => $transactionRef,
            'escrow_locked'   => 0,
        ]);

        create_notification($userId, 'Fine Payment Confirmed',
            "Fine payment of \${$amount} confirmed. Ref: {$transactionRef}", 'payment');

        $this->auditLog->log($userId, 'fine_payment',
            "Fine #{$fineId} paid \${$amount}. Payment #{$paymentId}");

        return ['success' => true, 'payment_id' => $paymentId, 'transaction_ref' => $transactionRef];
    }
}