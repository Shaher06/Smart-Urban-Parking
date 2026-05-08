<?php

require_once BASE_PATH . '/core/Model.php';

class Message extends Model
{
    protected string $table = 'messages';

    public function send(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO messages (sender_id, receiver_id, subject, body)
             VALUES (:sender_id,:receiver_id,:subject,:body)"
        );
        $stmt->execute([
            ':sender_id'   => $data['sender_id'],
            ':receiver_id' => $data['receiver_id'],
            ':subject'     => $data['subject'] ?? null,
            ':body'        => $data['body'],
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function getInbox(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT m.*, u.name as sender_name
             FROM messages m
             JOIN users u ON u.id = m.sender_id
             WHERE m.receiver_id = ?
             ORDER BY m.created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getSent(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT m.*, u.name as receiver_name
             FROM messages m
             JOIN users u ON u.id = m.receiver_id
             WHERE m.sender_id = ?
             ORDER BY m.created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function markRead(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE messages SET is_read=1 WHERE id=? AND receiver_id=?"
        );
        return $stmt->execute([$id, $userId]);
    }

    public function unreadCount(int $userId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM messages WHERE receiver_id=? AND is_read=0"
        );
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }
}