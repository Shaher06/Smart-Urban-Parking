<?php


require_once BASE_PATH . '/core/Model.php';

class Fine extends Model
{
    protected string $table = 'fines';

    // ── Create ────────────────────────────────────────────────────────────────

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO fines (user_id, reservation_id, issued_by, amount, reason, status)
             VALUES (:user_id, :reservation_id, :issued_by, :amount, :reason, :status)"
        );
        $stmt->execute([
            ':user_id'        => $data['user_id'],
            ':reservation_id' => $data['reservation_id'] ?? null,
            ':issued_by'      => $data['issued_by'] ?? null,
            ':amount'         => $data['amount'],
            ':reason'         => $data['reason'],
            ':status'         => $data['status'] ?? 'unpaid',
        ]);
        return (int)$this->db->lastInsertId();
    }

    // ── Update ────────────────────────────────────────────────────────────────

    /**
     * Generic field update — used by AppealService to restore status to 'unpaid'.
     */
    public function update(int $id, array $data): bool
    {
        $allowed = ['status', 'paid_at', 'amount', 'reason'];
        $fields  = [];
        $params  = [];

        foreach ($allowed as $col) {
            if (array_key_exists($col, $data)) {
                $fields[]          = "{$col} = :{$col}";
                $params[":{$col}"] = $data[$col];
            }
        }

        if (empty($fields)) return false;

        $params[':id'] = $id;
        $sql  = "UPDATE fines SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * updateStatus() — update only the status column.
     *
     * FIX: This method was missing, causing Fatal Error when called by:
     *   - FineService::payFine()   → sets status = 'paid'
     *   - FineService::waiveFine() → sets status = 'waived'
     *   - AppealService            → sets status = 'appealed'
     *
     * @param int    $id      Fine ID
     * @param string $status  One of: unpaid | paid | appealed | waived
     * @return bool
     */
    public function updateStatus(int $id, string $status): bool
    {
        $allowed = ['unpaid', 'paid', 'appealed', 'waived'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }

        if ($status === 'paid') {
            // Set paid_at timestamp when marking as paid
            $stmt = $this->db->prepare(
                "UPDATE fines SET status = ?, paid_at = NOW() WHERE id = ?"
            );
        } else {
            $stmt = $this->db->prepare(
                "UPDATE fines SET status = ? WHERE id = ?"
            );
        }

        return $stmt->execute([$status, $id]);
    }

    // ── Read ──────────────────────────────────────────────────────────────────

    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT f.*, u.name AS issued_by_name
             FROM fines f
             LEFT JOIN users u ON u.id = f.issued_by
             WHERE f.user_id = ?
             ORDER BY f.issued_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT f.*, u.name AS driver_name, ib.name AS officer_name
             FROM fines f
             JOIN  users u  ON u.id  = f.user_id
             LEFT JOIN users ib ON ib.id = f.issued_by
             ORDER BY f.issued_at DESC"
        );
        return $stmt->fetchAll();
    }

    /**
     * getStats() — aggregate statistics for admin dashboard.
     *
     * FIX: This method was missing — it was the cause of the original
     *      Fatal Error: "Call to undefined method Fine::getStats()"
     *      shown in the browser screenshot.
     *
     * Called by FineService::getFineStats() which is called by:
     *   - AdminController::dashboard()
     *   - FineController::adminFines()
     *
     * @return array  ['total', 'unpaid', 'paid', 'appealed', 'waived', 'revenue']
     */
    public function getStats(): array
    {
        $stmt = $this->db->query(
            "SELECT
                COUNT(*)                                                       AS total,
                SUM(status = 'unpaid')                                         AS unpaid,
                SUM(status = 'paid')                                           AS paid,
                SUM(status = 'appealed')                                       AS appealed,
                SUM(status = 'waived')                                         AS waived,
                COALESCE(SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END), 0) AS revenue
             FROM fines"
        );
        $row = $stmt->fetch();

        return [
            'total'    => (int)   ($row['total']    ?? 0),
            'unpaid'   => (int)   ($row['unpaid']   ?? 0),
            'paid'     => (int)   ($row['paid']     ?? 0),
            'appealed' => (int)   ($row['appealed'] ?? 0),
            'waived'   => (int)   ($row['waived']   ?? 0),
            'revenue'  => (float) ($row['revenue']  ?? 0.0),
        ];
    }

    // ── Status shortcuts ──────────────────────────────────────────────────────

    public function markPaid(int $id): bool
    {
        return $this->updateStatus($id, 'paid');
    }

    public function markAppealed(int $id): bool
    {
        return $this->updateStatus($id, 'appealed');
    }

    public function waive(int $id): bool
    {
        return $this->updateStatus($id, 'waived');
    }

    public function countUnpaid(int $userId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM fines WHERE user_id = ? AND status = 'unpaid'"
        );
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }
}