<?php

require_once BASE_PATH . '/core/Model.php';

class Notification extends Model
{
    protected string $table = 'notifications';

    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function markRead(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE notifications SET is_read=1 WHERE id=? AND user_id=?"
        );
        return $stmt->execute([$id, $userId]);
    }

    public function markAllRead(int $userId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE notifications SET is_read=1 WHERE user_id=?"
        );
        return $stmt->execute([$userId]);
    }

    public function unreadCount(int $userId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=0"
        );
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }
}