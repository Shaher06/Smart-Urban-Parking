<?php
/**
 * BookingService White-Box Tests
 */

define('BASE_PATH', dirname(__DIR__, 2) . '/src');
require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/helpers/auth_helper.php';
require_once BASE_PATH . '/helpers/notification_helper.php';
require_once BASE_PATH . '/models/Reservation.php';
require_once BASE_PATH . '/models/ParkingSpot.php';
require_once BASE_PATH . '/models/PromoCode.php';
require_once BASE_PATH . '/models/AuditLog.php';
require_once BASE_PATH . '/services/BookingService.php';

function assert_true(bool $condition, string $label): void
{
    echo ($condition ? "[PASS] " : "[FAIL] ") . $label . "\n";
}

$_SESSION['user_id'] = 2;
$_SESSION['user']    = ['id' => 2, 'role' => 'driver'];

$service = new BookingService();

// Test 1: Book available spot
$result = $service->book([
    'user_id'    => 2,
    'spot_id'    => 1,
    'start_time' => date('Y-m-d H:i:s', strtotime('+2 hours')),
    'end_time'   => date('Y-m-d H:i:s', strtotime('+3 hours')),
]);
assert_true($result['success'], "Book: Available spot booked successfully");

// Test 2: Cancel reservation
if ($result['success']) {
    $cancel = $service->cancel($result['reservation_id'], 2);
    assert_true($cancel['success'], "Cancel: Reservation cancelled");
    assert_true($cancel['refund_pct'] === 100, "Cancel: 100% refund for >2hr notice");
}

// Test 3: Book invalid spot
$bad = $service->book([
    'user_id'    => 2,
    'spot_id'    => 99999,
    'start_time' => date('Y-m-d H:i:s', strtotime('+4 hours')),
    'end_time'   => date('Y-m-d H:i:s', strtotime('+5 hours')),
]);
assert_true(!$bad['success'], "Book: Invalid spot returns failure");