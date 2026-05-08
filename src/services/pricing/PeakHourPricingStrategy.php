<?php
/**
 * PEAK HOUR PRICING STRATEGY
 * PATTERN: Strategy (Concrete implementation of PricingStrategy)
 *
 * Applies 1.5× multiplier during peak hours:
 *   Morning peak: 07:00 – 09:00
 *   Evening peak: 17:00 – 19:00
 *
 * WHY A SEPARATE CLASS?
 *   If peak-hour logic changes (e.g. 1.75× or different hours),
 *   only THIS file changes. No other code is touched.
 */

require_once BASE_PATH . '/interfaces/PricingStrategy.php';

class PeakHourPricingStrategy implements PricingStrategy
{
    private const MULTIPLIER = 1.5;

    public function calculate(float $pricePerHour, string $startTime, string $endTime): float
    {
        $hours        = (strtotime($endTime) - strtotime($startTime)) / 3600;
        $adjustedRate = $pricePerHour * self::MULTIPLIER;
        return round(max(0, $hours) * $adjustedRate, 2);
    }

    public function getName(): string
    {
        return 'Peak Hour Rate (1.5×)';
    }

    public static function isCurrentlyPeak(string $datetime): bool
    {
        $hour = (int) date('G', strtotime($datetime));
        return ($hour >= 7 && $hour < 9) || ($hour >= 17 && $hour < 19);
    }
}