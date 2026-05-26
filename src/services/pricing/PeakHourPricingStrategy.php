<?php
/**
 * PEAK HOUR PRICING STRATEGY
 * PATTERN: Strategy (Concrete implementation of PricingStrategy)

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