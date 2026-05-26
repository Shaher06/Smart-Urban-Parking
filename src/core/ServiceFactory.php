<?php


if (!class_exists('ServiceFactory')) {

    class ServiceFactory
    {
        /**
         * PATTERN: Factory
         *
         * Registry maps service aliases to [ClassName, relative-file-path].
         * Path is relative to BASE_PATH (i.e. src/).
         *
         * To add a new service: add one line here — zero controller changes needed.
         */
        private static array $registry = [
            'auth'         => ['AuthService',           'services/AuthService.php'],
            'booking'      => ['BookingService',        'services/BookingService.php'],
            'payment'      => ['PaymentGatewayService', 'services/PaymentGatewayService.php'],
            'escrow'       => ['EscrowService',         'services/EscrowService.php'],
            'fine'         => ['FineService',           'services/FineService.php'],
            'appeal'       => ['AppealService',         'services/AppealService.php'],
            'notification' => ['NotificationEngine',    'services/NotificationEngine.php'],
            'report'       => ['ReportService',         'services/ReportService.php'],
            'upload'       => ['UploadService',         'services/UploadService.php'],
            'navigation'   => ['NavigationService',     'services/NavigationService.php'],
            'audit'        => ['AuditTrailService',     'services/AuditTrailService.php'],
            'health'       => ['HealthMonitorService',  'services/HealthMonitorService.php'],
            'tax'          => ['TaxService',            'services/TaxService.php'],
            'pricing'      => ['PricingService',        'services/PricingService.php'],
            'role'         => ['RoleService',           'services/RoleService.php'],

            // FIX: CurrencyService lives in services/pricing/ — not services/ root.
            // Previously 'services/CurrencyService.php' caused FileNotFoundException.
            'currency'     => ['CurrencyService',       'services/pricing/CurrencyService.php'],

            // These two live in services/ root — correct paths:
            'pdf'          => ['PdfReportService',      'services/PdfReportService.php'],
            'occupancy'    => ['OccupancyService',      'services/OccupancyService.php'],
        ];

        /**
         * Cache of already-created instances.
         * Combines Factory with lightweight Flyweight: each service is new'd once.
         */
        private static array $instances = [];

        /**
         * FACTORY METHOD — returns the requested service instance.
         *
         * PATTERN: Factory
         * USED IN: Every controller (AdminController, FineController, etc.)
         *
         * @param string $name  Service alias from the registry above
         * @return object       The service instance (fresh or cached)
         * @throws RuntimeException  if alias unknown or file/class missing
         */
        public static function make(string $name): object
        {
            if (!isset(self::$registry[$name])) {
                throw new RuntimeException(
                    "ServiceFactory: Unknown service alias '{$name}'. "
                    . "Available: " . implode(', ', array_keys(self::$registry))
                );
            }

            // Return cached instance if already created this request
            if (isset(self::$instances[$name])) {
                return self::$instances[$name];
            }

            [$class, $relativePath] = self::$registry[$name];

            $fullPath = BASE_PATH . '/' . $relativePath;

            if (!file_exists($fullPath)) {
                throw new RuntimeException(
                    "ServiceFactory: File not found for service '{$name}': {$fullPath}"
                );
            }

            require_once $fullPath;

            if (!class_exists($class)) {
                throw new RuntimeException(
                    "ServiceFactory: Class '{$class}' not found after loading {$fullPath}"
                );
            }

            self::$instances[$name] = new $class();
            return self::$instances[$name];
        }

        /**
         * Register a custom service at runtime.
         * Useful for swapping real services with test mocks.
         *
         * @param string $name      Alias to register
         * @param string $class     Class name
         * @param string $file      Path relative to BASE_PATH
         */
        public static function register(string $name, string $class, string $file): void
        {
            self::$registry[$name] = [$class, $file];
            unset(self::$instances[$name]); // Clear cache so new class is used
        }

        /**
         * Inject a pre-built instance directly.
         * Primary use: unit testing — inject a mock object.
         *
         * @param string $name      Alias
         * @param object $instance  Pre-built instance
         */
        public static function inject(string $name, object $instance): void
        {
            self::$instances[$name] = $instance;
        }

        /**
         * Clear the instance cache.
         * Useful in tests to reset state between test cases.
         */
        public static function reset(): void
        {
            self::$instances = [];
        }
    }
}