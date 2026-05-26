<?php


// Ensure Config class is available (loaded via ServiceFactory in a fresh require)
if (!class_exists('Config')) {
    require_once BASE_PATH . '/config/app.php';
}

class CurrencyService
{
    private array  $rates;
    private string $defaultCurrency;

    public function __construct()
    {
        // PATTERN: Singleton — Config::getInstance() ensures one Config object
        $config                = Config::getInstance();
        $this->rates           = $config->get('currencies', ['USD' => 1.0]);
        $this->defaultCurrency = $config->get('default_currency', 'USD');
    }

    /**
     * Convert an amount from USD (the system base currency) to target currency.
     *
     * @param float  $amountUSD   Amount in US dollars
     * @param string $toCurrency  Target currency code (e.g. 'EGP', 'EUR')
     * @return float              Converted amount, rounded to 2 decimal places
     */
    public function convert(float $amountUSD, string $toCurrency): float
    {
        $rate = $this->rates[$toCurrency] ?? 1.0;
        return round($amountUSD * $rate, 2);
    }

    /**
     * Convert from any currency back to USD (for storage).
     * All amounts are stored in USD internally.
     *
     * @param float  $amount        Amount in source currency
     * @param string $fromCurrency  Source currency code
     * @return float                Equivalent in USD
     */
    public function toUSD(float $amount, string $fromCurrency): float
    {
        $rate = $this->rates[$fromCurrency] ?? 1.0;
        if ($rate == 0) return $amount;
        return round($amount / $rate, 2);
    }

    /**
     * Format amount with currency symbol for display.
     *
     * @param float  $amount
     * @param string $currency  Currency code
     * @return string           e.g. "$12.50" or "EGP 625.00"
     */
    public function format(float $amount, string $currency = 'USD'): string
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'AED' => 'AED ',
            'EGP' => 'EGP ',
            'SAR' => 'SAR ',
        ];
        $symbol = $symbols[$currency] ?? ($currency . ' ');
        return $symbol . number_format($amount, 2);
    }

    /**
     * Get all supported currency codes for dropdown menus in views.
     *
     * @return string[]
     */
    public function getSupportedCurrencies(): array
    {
        return array_keys($this->rates);
    }

    /**
     * Get the exchange rate for a currency relative to USD.
     *
     * @param string $currency
     * @return float
     */
    public function getRate(string $currency): float
    {
        return $this->rates[$currency] ?? 1.0;
    }

    /**
     * Get default currency configured in app settings.
     */
    public function getDefaultCurrency(): string
    {
        return $this->defaultCurrency;
    }
}