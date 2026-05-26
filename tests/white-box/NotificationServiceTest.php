<?php

define('BASE_PATH', dirname(__DIR__, 2) . '/src');
require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/interfaces/NotificationService.php';
require_once BASE_PATH . '/models/Notification.php';
require_once BASE_PATH . '/services/NotificationEngine.php';

function assert_true(bool $condition, string $label): void
{
    echo ($condition ? "[PASS] " : "[FAIL] ") . $label . "\n";
}

$engine = new NotificationEngine();

// Test 1: Send notification
$sent = $engine->send(2, 'Test', 'Test notification message', 'system');
assert_true($sent, "Send: Notification sent to user 2");

// Test 2: Get notifications
$notifs = $engine->getUserNotifications(2);
assert_true(is_array($notifs), "Get: Returns array");
assert_true(count($notifs) > 0, "Get: At least one notification");

// Test 3: Mark as read
$id     = $notifs[0]['id'];
$marked = $engine->markAsRead($id);
assert_true($marked, "Mark Read: Notification #{$id} marked as read");