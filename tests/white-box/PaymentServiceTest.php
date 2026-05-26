<?php


define('BASE_PATH', dirname(__DIR__, 2) . '/src');
require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/helpers/auth_helper.php';
require_once BASE_PATH . '/helpers/notification_helper.php';
require_once BASE_PATH . '/interfaces/PaymentService.php';
require_once BASE_PATH . '/models/Payment.php';
require_once BASE_PATH . '/models/AuditLog.php';
require_once BASE_PATH . '/services/PaymentGatewayService.php';

function assert_true(bool $condition, string $label): void
{
    echo ($condition ? "[PASS] " : "[FAIL] ") . $label . "\n";
}

$_SESSION['user_id'] = 2;
$_SESSION['user']    = ['id' => 2, 'role' => 'driver'];

$gateway = new PaymentGatewayService();

// Test 1: Charge returns success
$result = $gateway->charge(15.00, ['user_id' => 2, 'method' => 'credit_card']);
assert_true($result['success'], "Charge: Returns success");
assert_true(!empty($result['transaction_ref']), "Charge: Has transaction ref");

// Test 2: Status lookup
$status = $gateway->getStatus($result['transaction_ref']);
assert_true($status === 'completed', "Status: Completed after successful charge");

// Test 3: Refund simulation
$refund = $gateway->refund($result['transaction_ref'], 15.00);
assert_true($refund['success'], "Refund: Simulation returns success");
assert_true(!empty($refund['refund_ref']), "Refund: Has refund reference");