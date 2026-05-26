<?php


require_once BASE_PATH . '/interfaces/PricingStrategy.php';

class EventZonePricingStrategy implements PricingStrategy
{
    private const MULTIPLIER = 2.0;

    public function calculate(float $pricePerHour, string $startTime, string $endTime): float
    {
        $hours = (strtotime($endTime) - strtotime($startTime)) / 3600;
        return round(max(0, $hours) * $pricePerHour * self::MULTIPLIER, 2);
    }

    public function getName(): string
    {
        return 'Event Zone Rate (2×)';
    }
}