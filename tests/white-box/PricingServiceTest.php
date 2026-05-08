<?php
/**
 * PricingService — market suggestion and commission (no database).
 */
define('BASE_PATH', dirname(__DIR__, 2) . '/src');
require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/services/PricingService.php';

function assert_true(bool $condition, string $label): void
{
    echo ($condition ? "[PASS] " : "[FAIL] ") . $label . "\n";
}

$p = new PricingService();

$s = $p->suggestMarketHourly(5.0, 6.0, 2, 10);
assert_true($s > 0 && $s < 20, "Suggest: returns reasonable hourly rate (got {$s})");

$c = $p->calculateCommission(100.0);
assert_true(abs($c['commission'] - 10.0) < 0.01 && abs($c['net'] - 90.0) < 0.01, "Commission: 10% on 100");
