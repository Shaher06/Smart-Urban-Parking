<?php
/**
 * AuthService White-Box Tests
 * Run manually or integrate with a PHP test runner.
 * Tests cover internal logic branches of AuthService.
 */

define('BASE_PATH', dirname(__DIR__, 2) . '/src');
require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/helpers/auth_helper.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/AuditLog.php';
require_once BASE_PATH . '/services/AuthService.php';

function assert_true(bool $condition, string $label): void
{
    echo ($condition ? "[PASS] " : "[FAIL] ") . $label . "\n";
}

$service = new AuthService();

// Test 1: Login with valid credentials
$user = $service->login('driver@parking.com', 'password');
assert_true($user !== false, "Login: Valid driver credentials");

// Test 2: Login with wrong password
$result = $service->login('driver@parking.com', 'wrongpass');
assert_true($result === false, "Login: Wrong password returns false");

// Test 3: Login with non-existent email
$result = $service->login('nobody@test.com', 'password');
assert_true($result === false, "Login: Non-existent email returns false");

// Test 4: Register new user
$unique = 'test_' . time() . '@example.com';
$id = $service->register([
    'name'     => 'Test User',
    'email'    => $unique,
    'password' => 'pass123',
    'phone'    => '000',
    'role'     => 'driver',
]);
assert_true($id > 0, "Register: New user created with ID {$id}");

// Test 5: Register duplicate email
$dup = $service->register([
    'name'     => 'Dup',
    'email'    => $unique,
    'password' => 'pass123',
    'role'     => 'driver',
]);
assert_true($dup === false, "Register: Duplicate email returns false");