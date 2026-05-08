<?php
/**
 * PAYMENT MODEL
 *
 * PATTERN: Model layer — all payments table DB operations here.
 * PATTERN: Database::getInstance() — Singleton Pattern.
 *
 * FIX: Added missing methods:
 *   - updateStatus()      called by PaymentGatewayService::release()
 *   - getEscrowPayments() called by PaymentController::escrow()
 *
 * Payment lifecycle:
 *   pending → escrow → completed
 *   pending → failed
 *   completed → refunded
 */

require_once BASE_PATH . '/core/Model.php';

class Payment extends Model
{
    protected string $table = 'payments';

    // ── Create ────────────────────────────────────────────────────────────────

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO payments
                (user_id, reservation_id, fine_id, amount, method, status, transaction_ref, escrow_locked)
             VALUES
                (:user_id, :reservation_id, :fine_id, :amount, :method, :status, :transaction_ref, :escrow_locked)"
        );
        $stmt->execute([
            ':user_id'         => $data['user_id'],
            ':reservation_id'  => $data['reservation_id'] ?? null,
            ':fine_id'         => $data['fine_id'] ?? null,
            ':amount'          => $data['amount'],
            ':method'          => $data['method'] ?? 'credit_card',
            ':status'          => $data['status'] ?? 'escrow',
            ':transaction_ref' => $data['transaction_ref'] ?? ('TXN-' . strtoupper(bin2hex(random_bytes(6)))),
            ':escrow_locked'   => $data['escrow_locked'] ?? 1,
        ]);
        return (int)$this->db->lastInsertId();
    }

    // ── Update ────────────────────────────────────────────────────────────────

    /**
     * updateStatus() — update payment status column.
     *
     * FIX: This method was missing, causing Fatal Error when called by:
     *   - PaymentGatewayService::release() → sets status = 'completed'
     *   - PaymentGatewayService::refund()  → sets status = 'refunded'
     *
     * @param int    $id      Payment ID
     * @param string $status  One of the PAY_* constants
     * @return bool
     */
    public function updateStatus(int $id, string $status): bool
    {
        $allowed = ['pending', 'escrow', 'completed', 'failed', 'refunded'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }

        $stmt = $this->db->prepare(
            "UPDATE payments SET status = ? WHERE id = ?"
        );
        return $stmt->execute([$status, $id]);
    }

    /**
     * releaseEscrow() — mark funds as unlocked/released to owner.
     * Called after successful checkout: escrow → completed.
     */
    public function releaseEscrow(int $paymentId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE payments SET escrow_released = 1, escrow_locked = 0 WHERE id = ?"
        );
        return $stmt->execute([$paymentId]);
    }

    // ── Read ──────────────────────────────────────────────────────────────────

    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*,
                    r.start_time, r.end_time,
                    ps.name AS spot_name
             FROM payments p
             LEFT JOIN reservations r   ON r.id   = p.reservation_id
             LEFT JOIN parking_spots ps ON ps.id  = r.spot_id
             WHERE p.user_id = ?
             ORDER BY p.created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * getEscrowPayments() — returns payments still locked in escrow for a user.
     *
     * FIX: This method was missing — called by PaymentController::escrow()
     *      to show driver their locked funds.
     *
     * @param int $userId
     * @return array
     */
    public function getEscrowPayments(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*,
                    r.start_time, r.end_time,
                    ps.name AS spot_name
             FROM payments p
             LEFT JOIN reservations r   ON r.id   = p.reservation_id
             LEFT JOIN parking_spots ps ON ps.id  = r.spot_id
             WHERE p.user_id = ?
               AND p.status = 'escrow'
               AND p.escrow_locked = 1
             ORDER BY p.created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT p.*, u.name AS user_name
             FROM payments p
             JOIN users u ON u.id = p.user_id
             ORDER BY p.created_at DESC"
        );
        return $stmt->fetchAll();
    }

    public function getTotalRevenue(): float
    {
        $stmt = $this->db->query(
            "SELECT COALESCE(SUM(amount), 0)
             FROM payments
             WHERE status = 'completed'"
        );
        return (float)$stmt->fetchColumn();
    }
}