<?php

declare(strict_types=1);

// ── 1. Define core constants FIRST ───────────────────────────────────────────
// BASE_PATH and BASE_URL must exist before any require_once.
// All other files check if (!defined(...)) so these are safe to define here.

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

if (!defined('BASE_URL')) {
    define('BASE_URL', '/Smart_Parking/src/public');
}

// ── 2. Load core config and infrastructure ────────────────────────────────────
require_once BASE_PATH . '/config/app.php';       // Config Singleton + constants (PATTERN: Singleton)
require_once BASE_PATH . '/config/constants.php'; // Status/role/payment constants (guarded)
require_once BASE_PATH . '/config/database.php';  // Database Singleton          (PATTERN: Singleton)
require_once BASE_PATH . '/core/Session.php';
require_once BASE_PATH . '/core/ServiceFactory.php'; // Factory Pattern           (PATTERN: Factory)

// ── 3. Load helpers ───────────────────────────────────────────────────────────
require_once BASE_PATH . '/helpers/auth_helper.php';
require_once BASE_PATH . '/helpers/url_helper.php';
require_once BASE_PATH . '/helpers/flash_helper.php';
require_once BASE_PATH . '/helpers/report_helper.php';
require_once BASE_PATH . '/helpers/notification_helper.php';

// ── 4. Load core MVC ──────────────────────────────────────────────────────────
require_once BASE_PATH . '/core/Router.php';
require_once BASE_PATH . '/core/Validator.php';

// ── 5. Start session (one session per request — behaves like a Singleton) ─────
$session = new Session();

// ── 6. Register routes ────────────────────────────────────────────────────────
$router = new Router();

// Public landing
$router->add('home', 'HomeController', 'landing');

// Auth
$router->add('login',    'AuthController', 'login');
$router->add('register', 'AuthController', 'register');
$router->add('logout',   'AuthController', 'logout');

// User / Profile
$router->add('profile',          'UserController', 'profile');
$router->add('edit-profile',     'UserController', 'editProfile');
$router->add('delete-profile',   'UserController', 'deleteProfile');
$router->add('language',         'UserController', 'language');
$router->add('change-password',  'UserController', 'changePassword');

// Driver
$router->add('driver-dashboard',    'DriverController',      'dashboard');
$router->add('driver-emergency',    'DriverController',      'emergencyReport');
$router->add('browse-spots',        'ParkingSpotController', 'browse');
$router->add('nearby-spots',        'ParkingSpotController', 'nearby');
$router->add('book-spot',           'ReservationController', 'bookSpot');
$router->add('reservations',        'ReservationController', 'reservations');
$router->add('reservation-history', 'ReservationController', 'reservationHistory');
$router->add('cancel-reservation',  'ReservationController', 'cancelReservation');
$router->add('extend-reservation',  'ReservationController', 'extendReservation');
$router->add('check-in-out',        'ReservationController', 'checkInOut');
$router->add('navigate',            'ReservationController', 'navigate');
$router->add('vehicles',            'VehicleController',     'vehicles');
$router->add('add-vehicle',         'VehicleController',     'addVehicle');
$router->add('edit-vehicle',        'VehicleController',     'editVehicle');
$router->add('delete-vehicle',      'VehicleController',     'deleteVehicle');
$router->add('set-default-vehicle', 'VehicleController',     'setDefaultVehicle');
$router->add('fines',               'FineController',        'driverFines');
$router->add('pay-fine',            'FineController',        'payFine');
$router->add('appeal-fine',         'AppealController',      'appealFine');
$router->add('favorites',           'SystemController',      'favorites');
$router->add('toggle-favorite',     'SystemController',      'toggleFavorite');
$router->add('waitlist',            'WaitlistController',    'waitlist');
$router->add('join-waitlist',       'WaitlistController',    'joinWaitlist');
$router->add('leave-waitlist',      'WaitlistController',    'leaveWaitlist');
$router->add('driver-reviews',      'ReviewController',      'driverReviews');
$router->add('add-review',          'ReviewController',      'addReview');
$router->add('driver-messages',     'MessageController',     'driverMessages');
$router->add('send-message',        'MessageController',     'sendMessage');

// Owner
$router->add('owner-dashboard',    'OwnerController',       'dashboard');
$router->add('owner-spots',        'ParkingSpotController', 'ownerSpots');
$router->add('add-spot',           'ParkingSpotController', 'addSpot');
$router->add('edit-spot',          'ParkingSpotController', 'editSpot');
$router->add('delete-spot',        'ParkingSpotController', 'deleteSpot');
$router->add('owner-earnings',     'OwnerController',       'earnings');
$router->add('owner-payouts',      'OwnerController',       'payouts');
$router->add('owner-verification', 'OwnerController',       'verification');
$router->add('owner-availability', 'OwnerController',       'availability');
$router->add('owner-pricing',      'OwnerController',       'pricing');
$router->add('owner-reviews',      'ReviewController',      'ownerReviews');
$router->add('tax-details',        'OwnerController',       'taxDetails');
$router->add('owner-messages',     'MessageController',     'ownerMessages');

// Admin
$router->add('admin-dashboard',     'AdminController', 'dashboard');
$router->add('admin-users',         'AdminController', 'users');
$router->add('add-user',            'AdminController', 'addUser');
$router->add('delete-user',         'AdminController', 'deleteUser');
$router->add('update-user-status',  'AdminController', 'updateUserStatus');
$router->add('admin-roles',         'AdminController', 'roles');
$router->add('admin-blacklist',     'AdminController', 'blacklist');
$router->add('owners-verification', 'AdminController', 'ownersVerification');
$router->add('admin-fines',         'FineController',  'adminFines');
$router->add('issue-fine',          'FineController',  'issueFine');
$router->add('waive-fine',          'FineController',  'waiveFine');
$router->add('admin-appeals',       'AppealController','adminAppeals');
$router->add('review-appeal',       'AppealController','reviewAppeal');
$router->add('event-zones',         'AdminController', 'eventZones');
$router->add('emergency-override',  'AdminController', 'emergencyOverride');
$router->add('officer-dispatch',    'AdminController', 'officerDispatch');
$router->add('admin-emergency-reports', 'AdminController', 'emergencyReports');
$router->add('system-health',       'AdminController', 'systemHealth');
$router->add('audit-logs',          'AdminController', 'auditLogs');
$router->add('admin-reports',       'ReportController','adminReports');
$router->add('manage-payouts',      'AdminController', 'managePayouts');
$router->add('occupancy',           'AdminController', 'occupancyDashboard');

// Reports
$router->add('generate-report', 'ReportController', 'generate');
$router->add('revenue',         'ReportController', 'revenue');
$router->add('heatmap',         'ReportController', 'heatmap');
$router->add('export-pdf',      'ReportController', 'exportPdf');
$router->add('occupancy-report','ReportController', 'occupancy');

// Payment
$router->add('payment-history', 'PaymentController', 'paymentHistory');
$router->add('make-payment',    'PaymentController', 'makePayment');
$router->add('receipt',         'PaymentController', 'receipt');
$router->add('escrow',          'PaymentController', 'escrow');
$router->add('promo-code',      'PaymentController', 'promoCode');

// Notifications
$router->add('notifications',          'NotificationController', 'list');
$router->add('mark-notification-read', 'NotificationController', 'markRead');

// ── 7. Dispatch request ───────────────────────────────────────────────────────
$page = trim($_GET['page'] ?? '');

if ($page === '') {
    if (is_logged_in()) {
        $role     = current_role();
        $redirect = match($role) {
            'admin', 'officer' => '?page=admin-dashboard',
            'owner'            => '?page=owner-dashboard',
            default            => '?page=driver-dashboard',
        };
        header('Location: ' . BASE_URL . '/index.php' . $redirect);
    } else {
        header('Location: ' . BASE_URL . '/index.php?page=home');
    }
    exit;
}

$router->dispatch($page);