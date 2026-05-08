<?php

class PricingService
{
    public function calculatePrice(float $pricePerHour, string $start, string $end, bool $isPeakHour = false): float
    {
        $hours     = (strtotime($end) - strtotime($start)) / 3600;
        $basePrice = $hours * $pricePerHour;

        if ($isPeakHour) {
            $basePrice *= 1.5;
        }
        return round($basePrice, 2);
    }

    public function isPeakHour(string $datetime): bool
    {
        $hour = (int)date('H', strtotime($datetime));
        return ($hour >= 7 && $hour <= 9) || ($hour >= 17 && $hour <= 19);
    }

    public function calculateOverstayPenalty(float $pricePerHour, int $overstayMinutes): float
    {
        $hours   = $overstayMinutes / 60;
        $penalty = $hours * $pricePerHour * OVERSTAY_RATE;
        return round($penalty, 2);
    }

    public function applyDiscount(float $price, float $discountPercent): float
    {
        return round($price * (1 - $discountPercent / 100), 2);
    }

    public function calculateCommission(float $grossAmount): array
    {
        $commission = $grossAmount * COMMISSION_RATE;
        $net        = $grossAmount - $commission;
        return ['gross' => $grossAmount, 'commission' => $commission, 'net' => $net];
    }

    /**
     * Simple market-style suggestion from city average and current occupancy.
     */
    public function suggestMarketHourly(
        float $currentHourly,
        float $cityAvgHourly,
        int $availableSlots,
        int $totalSlots
    ): float {
        $totalSlots = max(1, $totalSlots);
        $vacancy    = $availableSlots / $totalSlots;
        $anchor     = $cityAvgHourly > 0
            ? ($cityAvgHourly * 0.55 + $currentHourly * 0.45)
            : $currentHourly * 1.05;
        if ($vacancy < 0.15) {
            $anchor *= 1.10;
        } elseif ($vacancy > 0.65) {
            $anchor *= 0.94;
        }
        return round(max(0.5, $anchor), 2);
    }
}