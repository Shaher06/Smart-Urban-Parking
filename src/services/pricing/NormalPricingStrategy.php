<?php
/**
 * NORMAL PRICING STRATEGY
 * PATTERN: Strategy (Concrete implementation of PricingStrategy)
 *
 * Applies standard hourly rate with no multiplier.
 * Used during off-peak hours.
 */

require_once BASE_PATH . '/interfaces/PricingStrategy.php';

class NormalPricingStrategy implements PricingStrategy
{
    public function calculate(float $pricePerHour, string $startTime, string $endTime): float
    {
        $hours = $this->getHours($startTime, $endTime);
        return round($hours * $pricePerHour, 2);
    }

    public function getName(): string
    {
        return 'Normal Rate';
    }

    protected function getHours(string $start, string $end): float
    {
        $diff = strtotime($end) - strtotime($start);
        return max(0, $diff / 3600);
    }
}