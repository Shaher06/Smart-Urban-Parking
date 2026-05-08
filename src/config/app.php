<?php
/**
 * APP CONFIGURATION
 *
 * PATTERN: Singleton applied to Config class
 *
 * WHY SINGLETON FOR CONFIG?
 *   Configuration is read many times across the app (services, controllers, views).
 *   Loading it once and caching in a single instance avoids repeated file reads
 *   and guarantees all parts of the app see the same config values.
 *
 * FIX: All define() calls use if (!defined(...)) guards.
 *      BASE_PATH is defined in index.php first — app.php must NOT redefine it.
 */

// ── Core constants ─────────────────────────────────────────────────────────────
// NOTE: BASE_PATH and BASE_URL are defined in index.php BEFORE this file is loaded.
// We only define them here as a safety fallback (e.g. if called from CLI/tests).

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

if (!defined('BASE_URL')) {
    define('BASE_URL', '/Smart_Parking/src/public');
}

if (!defined('APP_NAME')) {
    define('APP_NAME', 'Smart Urban Parking');
}

if (!defined('APP_VERSION')) {
    define('APP_VERSION', '1.0.0');
}

// ── Upload path ────────────────────────────────────────────────────────────────
// FIX: UPLOAD_PATH points to the physical folder on disk.
// upload_url() in url_helper.php maps this to a web-accessible URL.
// The folder must be INSIDE public/ so Apache/Nginx can serve it.
if (!defined('UPLOAD_PATH')) {
    define('UPLOAD_PATH', BASE_PATH . '/public/uploads');
}

// ── Business rules ─────────────────────────────────────────────────────────────
if (!defined('COMMISSION_RATE'))  define('COMMISSION_RATE',  0.10);  // 10% platform cut
if (!defined('TAX_RATE'))         define('TAX_RATE',         0.10);  // 10% income tax
if (!defined('VAT_RATE'))         define('VAT_RATE',         0.15);  // 15% VAT
if (!defined('BUFFER_MINUTES'))   define('BUFFER_MINUTES',   10);    // Buffer between bookings (SRS)
if (!defined('GRACE_MINUTES'))    define('GRACE_MINUTES',    5);     // Grace before no-show / overstay fine (SRS)
if (!defined('OVERSTAY_RATE'))    define('OVERSTAY_RATE',    2.0);   // Multiplier for overstay
if (!defined('MAX_UNPAID_FINES')) define('MAX_UNPAID_FINES', 3);     // Auto-blacklist threshold

// Loyalty: repeat customers get a small automatic discount on parking total
if (!defined('LOYALTY_MIN_BOOKINGS'))     define('LOYALTY_MIN_BOOKINGS',     5);
if (!defined('LOYALTY_DISCOUNT_PERCENT')) define('LOYALTY_DISCOUNT_PERCENT', 5);

// ── Config Singleton class ─────────────────────────────────────────────────────
if (!class_exists('Config')) {

    /**
     * PATTERN: Singleton
     * USED IN: Config::getInstance() throughout services and controllers.
     *
     * Wraps all app settings in a single, globally accessible object.
     * Prevents multiple instances — config is loaded exactly once per request.
     */
    class Config
    {
        // The single instance — static so it persists across calls
        private static ?Config $instance = null;

        private array $settings = [];

        /**
         * Private constructor — only getInstance() can create Config.
         * This enforces the Singleton pattern.
         */
        private function __construct()
        {
            $this->settings = [
                'app_name'         => APP_NAME,
                'app_version'      => APP_VERSION,
                'base_url'         => BASE_URL,
                'base_path'        => BASE_PATH,
                'upload_path'      => UPLOAD_PATH,
                'commission_rate'  => COMMISSION_RATE,
                'vat_rate'         => VAT_RATE,
                'buffer_minutes'   => BUFFER_MINUTES,
                'grace_minutes'    => GRACE_MINUTES,
                'overstay_rate'    => OVERSTAY_RATE,
                'max_unpaid_fines' => MAX_UNPAID_FINES,

                // SRS: Multi-Currency Settlement
                // Rates relative to USD as base currency.
                // In production: fetch from a live FX API (e.g. fixer.io).
                'currencies' => [
                    'USD' => 1.0,
                    'EUR' => 0.92,
                    'GBP' => 0.79,
                    'AED' => 3.67,
                    'EGP' => 50.0,
                    'SAR' => 3.75,
                ],
                'default_currency' => 'USD',
            ];
        }

        // Prevent cloning — another Singleton enforcement rule
        private function __clone() {}

        /**
         * PATTERN: Singleton — the single global access point.
         *
         * @return Config
         */
        public static function getInstance(): Config
        {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Get a config value by key.
         *
         * @param string $key
         * @param mixed  $default  Returned if key doesn't exist
         * @return mixed
         */
        public function get(string $key, mixed $default = null): mixed
        {
            return $this->settings[$key] ?? $default;
        }

        /**
         * Set or override a config value at runtime.
         * Useful for tests or dynamic config changes.
         */
        public function set(string $key, mixed $value): void
        {
            $this->settings[$key] = $value;
        }

        /**
         * Get all settings as an array.
         */
        public function all(): array
        {
            return $this->settings;
        }
    }
}