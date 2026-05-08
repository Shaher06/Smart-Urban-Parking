<?php
/**
 * PRICING STRATEGY INTERFACE — STRATEGY PATTERN
 *
 * WHY STRATEGY PATTERN FOR PRICING?
 *   Different pricing rules apply in different situations:
 *   - Normal hours: base rate
 *   - Peak hours (7-9am, 5-7pm): 1.5× rate
 *   - Event zones: 2× rate
 *   - Subscription users: fixed rate
 *
 *   Instead of one giant if/else block, each strategy is its own class.
 *   The PricingContext selects the right strategy at runtime.
 *
 * HOW IT WORKS:
 *   1. PricingContext holds a PricingStrategy.
 *   2. BookingService calls $context->calculate() without knowing which strategy runs.
 *   3. To add a new pricing rule: create a new class implementing PricingStrategy.
 *      Zero changes needed in BookingService.
 */
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