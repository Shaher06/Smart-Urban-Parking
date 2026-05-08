<?php

function create_notification(int $userId, string $title, string $message, string $type = 'system'): void
{
    try {
        $db   = Database::getInstance();
        $stmt = $db->prepare(
            "INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$userId, $title, $message, $type]);
    } catch (PDOException $e) {
        // silent fail — notifications are non-critical
    }
}

function unread_notification_count(int $userId): int
{
    try {
        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}
/**
 * Notify waiting users when a spot becomes available (Priority 9 — Waitlist).
 */
function notify_waitlist_for_spot(int $spotId): void
{
    $db   = Database::getInstance();
    $stmt = $db->prepare(
        "SELECT w.id, w.user_id, ps.name as spot_name
         FROM waitlist w
         JOIN parking_spots ps ON ps.id = w.spot_id
         WHERE w.spot_id = ? AND w.status = 'waiting'
         ORDER BY w.created_at ASC LIMIT 5"
    );
    $stmt->execute([$spotId]);
    $waiters = $stmt->fetchAll();
    foreach ($waiters as $w) {
        create_notification(
            (int)$w['user_id'],
            'Spot Now Available!',
            "A slot has opened at {$w['spot_name']}. Book now before it fills up!",
            'booking'
        );
        $db->prepare("UPDATE waitlist SET status='notified' WHERE id=?")->execute([$w['id']]);
    }
}
