<?php
/**
 * DATABASE CONNECTION — SINGLETON PATTERN
 *
 * PATTERN: Singleton

 */

if (!class_exists('Database')) {

    class Database
    {
        // The single static PDO instance — persists across all calls
        private static ?PDO $instance = null;

        // ── Connection settings — change these to match your environment ──────
        private string $host    = 'localhost';
        private string $dbname  = 'parking_system';
        private string $user    = 'root';
        private string $pass    = '';           // Change in production!
        private string $charset = 'utf8mb4';

        /**
         * Private constructor prevents direct instantiation via `new Database()`.
         * This is the core enforcement of Singleton.
         */
        private function __construct() {}

        /**
         * Prevent cloning — would create a second instance, breaking Singleton.
         */
        private function __clone() {}

        /**
         * PATTERN: Singleton — getInstance() is the ONLY way to get a connection.
         *
         * Called like: Database::getInstance()->prepare(...)
         *
         * @return PDO  The single shared PDO connection
         */
        public static function getInstance(): PDO
        {
            if (self::$instance === null) {
                $db  = new self();
                $dsn = "mysql:host={$db->host};dbname={$db->dbname};charset={$db->charset}";

                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ];

                try {
                    self::$instance = new PDO($dsn, $db->user, $db->pass, $options);
                } catch (PDOException $e) {
                    // In production: log to file instead of exposing DB details.
                    error_log('Database connection failed: ' . $e->getMessage());
                    die(
                        '<h2 style="color:red;font-family:Arial">Database connection failed.</h2>'
                        . '<p>Check your database credentials in <code>src/config/database.php</code>.</p>'
                    );
                }
            }

            return self::$instance;
        }
    }
}
