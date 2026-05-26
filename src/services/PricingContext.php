<?php


require_once BASE_PATH . '/interfaces/PricingStrategy.php';
require_once BASE_PATH . '/services/pricing/NormalPricingStrategy.php';
require_once BASE_PATH . '/services/pricing/PeakHourPricingStrategy.php';
require_once BASE_PATH . '/services/pricing/EventZonePricingStrategy.php';

class PricingContext
{
    private PricingStrategy $strategy;

    public function __construct(PricingStrategy $strategy = null)
    {
        // Default to normal pricing if none provided
        $this->strategy = $strategy ?? new NormalPricingStrategy();
    }

    /**
     * Change strategy at runtime.
     * e.g. $ctx->setStrategy(new EventZonePricingStrategy());
     */
    public function setStrategy(PricingStrategy $strategy): void
    {
        $this->strategy = $strategy;
    }

    /**
     * Delegate to the active strategy.
     */
    public function calculate(float $pricePerHour, string $startTime, string $endTime): float
    {
        return $this->strategy->calculate($pricePerHour, $startTime, $endTime);
    }

    public function getStrategyName(): string
    {
        return $this->strategy->getName();
    }

    /**
     * FACTORY METHOD inside Context:
     * Automatically selects correct strategy based on booking time and zone.
     *
     * PATTERN: Uses both Strategy (selection) and Factory (creation).
     */
    public static function forBooking(string $startTime, bool $isEventZone = false): self
    {
        if ($isEventZone) {
            return new self(new EventZonePricingStrategy());
        }

        if (PeakHourPricingStrategy::isCurrentlyPeak($startTime)) {
            return new self(new PeakHourPricingStrategy());
        }

        return new self(new NormalPricingStrategy());
    }
}