<?php

require_once BASE_PATH . '/core/Model.php';

class Waitlist extends Model
{
    protected string $table = 'waitlist';

    public function add(int $userId, int $spotId, string $start, string $end): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO waitlist (user_id, spot_id, requested_start, requested_end, status)
             VALUES (?,?,?,?,'waiting')"
        );
        $stmt->execute([$userId, $spotId, $start, $end]);
        return (int)$this->db->lastInsertId();
    }

    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT w.*, ps.name as spot_name, ps.city
             FROM waitlist w
             JOIN parking_spots ps ON ps.id = w.spot_id
             WHERE w.user_id = ?
             ORDER BY w.created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getBySpot(int $spotId): array
    {
        $stmt = $this->db->prepare(
            "SELECT w.*, u.name as user_name, u.email
             FROM waitlist w
             JOIN users u ON u.id = w.user_id
             WHERE w.spot_id = ? AND w.status = 'waiting'
             ORDER BY w.created_at ASC"
        );
        $stmt->execute([$spotId]);
        return $stmt->fetchAll();
    }

    public function isAlreadyWaiting(int $userId, int $spotId): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM waitlist WHERE user_id=? AND spot_id=? AND status='waiting'"
        );
        $stmt->execute([$userId, $spotId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function cancel(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE waitlist SET status='expired' WHERE id=? AND user_id=?"
        );
        return $stmt->execute([$id, $userId]);
    }
}