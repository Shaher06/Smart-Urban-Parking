<?php



require_once BASE_PATH . '/models/Notification.php';

class NotificationEngine
{
    private Notification $model;

    public function __construct()
    {
        $this->model = new Notification();
    }

    public function send(int $userId, string $title, string $message, string $type = 'system'): bool
    {
        try {
            $db   = Database::getInstance();
            $stmt = $db->prepare(
                "INSERT INTO notifications (user_id, title, message, type) VALUES (?,?,?,?)"
            );
            return $stmt->execute([$userId, $title, $message, $type]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function markAsRead(int $notificationId): bool
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("UPDATE notifications SET is_read=1 WHERE id=?");
        return $stmt->execute([$notificationId]);
    }

    public function getUserNotifications(int $userId): array
    {
        return $this->model->getByUser($userId);
    }

    public function escalate(int $userId, string $message): void
    {
        $this->send($userId, 'URGENT: Action Required', $message, 'system');
        // In a real system: also trigger SMS/Email via external provider
    }
}