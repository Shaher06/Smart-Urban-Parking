<?php
/**
 * FineService White-Box Tests
 */

define('BASE_PATH', dirname(__DIR__, 2) . '/src');
require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/helpers/auth_helper.php';
require_once BASE_PATH . '/helpers/notification_helper.php';
require_once BASE_PATH . '/models/Fine.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/AuditLog.php';
require_once BASE_PATH . '/models/Payment.php';
require_once BASE_PATH . '/interfaces/PaymentService.php';
require_once BASE_PATH . '/services/PaymentGatewayService.php';
require_once BASE_PATH . '/services/FineService.php';

function assert_true(bool $condition, string $label): void
{
    echo ($condition ? "[PASS] " : "[FAIL] ") . $label . "\n";
}

$_SESSION['user_id'] = 2;

$service = new FineService();

// Test 1: Issue fine
$fineId = $service->issueFine(2, 50.00, 'Overstay violation', 1);
assert_true($fineId > 0, "Issue Fine: Fine created with ID {$fineId}");

// Test 2: Pay fine
$pay = $service->payFine($fineId, 2, 'credit_card');
assert_true($pay['success'], "Pay Fine: Fine paid successfully");

// Test 3: Pay already paid fine
$pay2 = $service->payFine($fineId, 2, 'credit_card');
assert_true(!$pay2['success'], "Pay Fine: Cannot pay already-paid fine");