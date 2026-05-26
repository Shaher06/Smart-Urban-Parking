<?php

interface PricingStrategy
{
    /**
     * Calculate the total price for a parking session.
     *
     * @param float  $pricePerHour  Base rate from parking_spots table
     * @param string $startTime     ISO datetime string
     * @param string $endTime       ISO datetime string
     * @return float                Total calculated price
     */
    public function calculate(float $pricePerHour, string $startTime, string $endTime): float;

    /**
     * Human-readable name of this strategy (for UI display and audit logs).
     */
    public function getName(): string;
}