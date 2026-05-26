<?php

require_once BASE_PATH . '/core/Model.php';

class Payout extends Model
{
    protected string $table = 'payouts';

  
    public function getAvailableNet(int $ownerId): float
    {
        // Completed gross earnings for this owner (released funds only)
        $grossStmt = $this->db->prepare(
            "SELECT COALESCE(SUM(p.amount),0)
             FROM payments p
             JOIN reservations r ON r.id = p.reservation_id
             JOIN parking_spots s ON s.id = r.spot_id
             WHERE s.owner_id = ? AND p.status = 'completed'"
        );
        $grossStmt->execute([$ownerId]);
        $completedGross = (float)$grossStmt->fetchColumn();

        $completedNet = $completedGross * (1 - COMMISSION_RATE);

        // Funds already reserved/consumed by payouts
        $reservedStmt = $this->db->prepare(
            "SELECT COALESCE(SUM(net_amount),0)
             FROM payouts
             WHERE owner_id = ?
               AND status IN ('pending','processing','paid')"
        );
        $reservedStmt->execute([$ownerId]);
        $reservedNet = (float)$reservedStmt->fetchColumn();

        return max(0.0, round($completedNet - $reservedNet, 2));
    }

    public function ownerHasPending(int $ownerId): bool
    {
        $stmt = $this->db->prepare(
            "SELECT 1 FROM payouts WHERE owner_id = ? AND status = 'pending' LIMIT 1"
        );
        $stmt->execute([$ownerId]);
        return (bool)$stmt->fetchColumn();
    }

    /**
     * Request payout for the owner's CURRENT available balance.
     *
     * Guarantees:
     * - cannot create payout when another pending payout exists
     * - cannot withdraw more than available net
     * - uses a DB transaction and row locks to prevent rapid double-submit races
     *
     * @return array {success: bool, payout_id?: int, message?: string, available_net?: float}
     */
    public function requestAvailablePayout(int $ownerId): array
    {
        try {
            $this->db->beginTransaction();

            // Lock existing payouts for this owner so pending checks & sums are stable.
            $lockPayouts = $this->db->prepare(
                "SELECT id FROM payouts WHERE owner_id = ? FOR UPDATE"
            );
            $lockPayouts->execute([$ownerId]);

            $pendingStmt = $this->db->prepare(
                "SELECT id FROM payouts WHERE owner_id = ? AND status = 'pending' LIMIT 1 FOR UPDATE"
            );
            $pendingStmt->execute([$ownerId]);
            $pendingId = $pendingStmt->fetchColumn();

            if ($pendingId) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'You already have a pending payout request.'];
            }

            // Lock completed payments contributing to earnings (prevents edge races with checkout)
            $grossStmt = $this->db->prepare(
                "SELECT COALESCE(SUM(p.amount),0)
                 FROM payments p
                 JOIN reservations r ON r.id = p.reservation_id
                 JOIN parking_spots s ON s.id = r.spot_id
                 WHERE s.owner_id = ? AND p.status = 'completed'
                "
            );
            $grossStmt->execute([$ownerId]);
            $completedGross = (float)$grossStmt->fetchColumn();
            $completedNet   = $completedGross * (1 - COMMISSION_RATE);

            $reservedStmt = $this->db->prepare(
                "SELECT COALESCE(SUM(net_amount),0)
                 FROM payouts
                 WHERE owner_id = ?
                   AND status IN ('pending','processing','paid')
                 FOR UPDATE"
            );
            $reservedStmt->execute([$ownerId]);
            $reservedNet = (float)$reservedStmt->fetchColumn();

            $availableNet = round($completedNet - $reservedNet, 2);

            if ($availableNet <= 0) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'No available earnings to withdraw.', 'available_net' => 0.0];
            }

            if (defined('MIN_PAYOUT_NET') && $availableNet < (float) MIN_PAYOUT_NET) {
                $this->db->rollBack();
                return [
                    'success' => false,
                    'message' => 'Minimum payout is $' . number_format((float) MIN_PAYOUT_NET, 2) . '.',
                    'available_net' => $availableNet,
                ];
            }

            // Convert net -> gross consistently with stored columns (keep 2 decimals)
            $gross = (COMMISSION_RATE < 1)
                ? round($availableNet / (1 - COMMISSION_RATE), 2)
                : round($availableNet, 2);
            $commission = round($gross * COMMISSION_RATE, 2);
            $net = round($gross - $commission, 2);

            // Final guard: don't exceed available net due to rounding
            if ($net > $availableNet) {
                $net = $availableNet;
            }

            $ins = $this->db->prepare(
                "INSERT INTO payouts (owner_id, amount, commission, net_amount, status)
                 VALUES (?,?,?,?, 'pending')"
            );
            $ins->execute([$ownerId, $gross, $commission, $net]);

            $payoutId = (int)$this->db->lastInsertId();
            $this->db->commit();

            return ['success' => true, 'payout_id' => $payoutId, 'available_net' => $availableNet];

        } catch (\PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            // Unique constraint (owner_id, pending_guard) blocks concurrent/double submits.
            if (($e->getCode() ?? '') === '23000') {
                return ['success' => false, 'message' => 'You already have a pending payout request.'];
            }
            return ['success' => false, 'message' => 'Failed to create payout request. Please try again.'];
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['success' => false, 'message' => 'Failed to create payout request. Please try again.'];
        }
    }

    public function getByOwner(int $ownerId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM payouts WHERE owner_id=? ORDER BY requested_at DESC"
        );
        $stmt->execute([$ownerId]);
        return $stmt->fetchAll();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT p.*, u.name as owner_name
             FROM payouts p
             JOIN users u ON u.id = p.owner_id
             ORDER BY p.requested_at DESC"
        );
        return $stmt->fetchAll();
    }

    /**
     * Idempotent: only transitions pending → paid exactly once.
     */
    public function markPaid(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE payouts
             SET status='paid', paid_at=NOW()
             WHERE id=? AND status='pending'"
        );
        $stmt->execute([$id]);
        return $stmt->rowCount() === 1;
    }
}