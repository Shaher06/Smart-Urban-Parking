CREATE DATABASE IF NOT EXISTS parking_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE parking_system;

-- =============================================
-- TABLE: users
-- =============================================
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    role ENUM('driver','owner','admin','officer') NOT NULL DEFAULT 'driver',
    status ENUM('active','suspended','blacklisted') NOT NULL DEFAULT 'active',
    language VARCHAR(10) NOT NULL DEFAULT 'en',
    profile_image VARCHAR(255) DEFAULT NULL,
    default_vehicle_id INT UNSIGNED DEFAULT NULL COMMENT 'Preferred vehicle for booking (drivers)',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =============================================
-- TABLE: vehicles
-- =============================================
CREATE TABLE vehicles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    plate_number VARCHAR(30) NOT NULL,
    make VARCHAR(50) DEFAULT NULL,
    model VARCHAR(50) DEFAULT NULL,
    color VARCHAR(30) DEFAULT NULL,
    year INT DEFAULT NULL,
    is_ev TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- TABLE: parking_spots
-- =============================================
CREATE TABLE parking_spots (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    owner_id INT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    latitude DECIMAL(10,7) DEFAULT NULL,
    longitude DECIMAL(10,7) DEFAULT NULL,
    type ENUM('public','private','reserved') NOT NULL DEFAULT 'public',
    price_per_hour DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    total_slots INT UNSIGNED NOT NULL DEFAULT 1,
    available_slots INT UNSIGNED NOT NULL DEFAULT 1,
    ev_support TINYINT(1) NOT NULL DEFAULT 0,
    status ENUM('active','inactive','maintenance','locked','owner-use') NOT NULL DEFAULT 'active',
    description TEXT DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    max_vehicle_height_m DECIMAL(5,2) DEFAULT NULL COMMENT 'Max vehicle height (meters) the spot accepts',
    max_vehicle_width_m DECIMAL(5,2) DEFAULT NULL COMMENT 'Max vehicle width (meters)',
    difficulty_rating TINYINT UNSIGNED NOT NULL DEFAULT 3 COMMENT '1=easy .. 5=tight access',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- TABLE: promo_codes (before reservations — FK on promo_code_id)
-- =============================================
CREATE TABLE promo_codes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    discount_percent DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    max_uses INT UNSIGNED NOT NULL DEFAULT 1,
    used_count INT UNSIGNED NOT NULL DEFAULT 0,
    valid_from DATETIME NOT NULL,
    valid_until DATETIME NOT NULL,
    status ENUM('active','expired','disabled') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- TABLE: reservations
-- =============================================
CREATE TABLE reservations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    spot_id INT UNSIGNED NOT NULL,
    vehicle_id INT UNSIGNED DEFAULT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    actual_checkin DATETIME DEFAULT NULL,
    actual_checkout DATETIME DEFAULT NULL,
    status ENUM('pending','confirmed','active','completed','cancelled','no_show') NOT NULL DEFAULT 'pending',
    total_price DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    refund_amount DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    qr_code VARCHAR(100) DEFAULT NULL,
    promo_code_id INT UNSIGNED DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (spot_id) REFERENCES parking_spots(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL,
    FOREIGN KEY (promo_code_id) REFERENCES promo_codes(id) ON DELETE SET NULL
);

-- =============================================
-- TABLE: fines (before payments — payments.fine_id FK)
-- =============================================
CREATE TABLE fines (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    reservation_id INT UNSIGNED DEFAULT NULL,
    issued_by INT UNSIGNED DEFAULT NULL,
    amount DECIMAL(8,2) NOT NULL,
    reason VARCHAR(255) NOT NULL,
    status ENUM('unpaid','paid','appealed','waived') NOT NULL DEFAULT 'unpaid',
    issued_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    paid_at DATETIME DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE SET NULL,
    FOREIGN KEY (issued_by) REFERENCES users(id) ON DELETE SET NULL
);

-- =============================================
-- TABLE: payments
-- =============================================
CREATE TABLE payments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    reservation_id INT UNSIGNED DEFAULT NULL,
    fine_id INT UNSIGNED DEFAULT NULL,
    amount DECIMAL(8,2) NOT NULL,
    method ENUM('credit_card','debit_card','wallet','cash') NOT NULL DEFAULT 'credit_card',
    status ENUM('pending','escrow','completed','failed','refunded') NOT NULL DEFAULT 'pending',
    transaction_ref VARCHAR(100) DEFAULT NULL,
    escrow_locked TINYINT(1) NOT NULL DEFAULT 0,
    escrow_released TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE SET NULL,
    FOREIGN KEY (fine_id) REFERENCES fines(id) ON DELETE SET NULL
);

-- =============================================
-- TABLE: appeals
-- =============================================
CREATE TABLE appeals (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fine_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    reason TEXT NOT NULL,
    evidence_file VARCHAR(255) DEFAULT NULL,
    status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    admin_note TEXT DEFAULT NULL,
    reviewed_by INT UNSIGNED DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (fine_id) REFERENCES fines(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
);

-- =============================================
-- TABLE: notifications
-- =============================================
CREATE TABLE notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('booking','payment','fine','appeal','system','message') NOT NULL DEFAULT 'system',
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- TABLE: reviews
-- =============================================
CREATE TABLE reviews (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    spot_id INT UNSIGNED NOT NULL,
    reservation_id INT UNSIGNED DEFAULT NULL,
    rating TINYINT UNSIGNED NOT NULL DEFAULT 3,
    comment TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (spot_id) REFERENCES parking_spots(id) ON DELETE CASCADE
);

-- =============================================
-- TABLE: messages
-- =============================================
CREATE TABLE messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sender_id INT UNSIGNED NOT NULL,
    receiver_id INT UNSIGNED NOT NULL,
    subject VARCHAR(200) DEFAULT NULL,
    body TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- TABLE: waitlist
-- =============================================
CREATE TABLE waitlist (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    spot_id INT UNSIGNED NOT NULL,
    requested_start DATETIME NOT NULL,
    requested_end DATETIME NOT NULL,
    status ENUM('waiting','notified','booked','expired') NOT NULL DEFAULT 'waiting',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (spot_id) REFERENCES parking_spots(id) ON DELETE CASCADE
);

-- =============================================
-- TABLE: audit_logs
-- =============================================
CREATE TABLE audit_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    ip_address VARCHAR(50) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- =============================================
-- TABLE: event_zones
-- =============================================
CREATE TABLE event_zones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    affected_spot_ids TEXT DEFAULT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    locked_by INT UNSIGNED DEFAULT NULL,
    status ENUM('active','expired') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (locked_by) REFERENCES users(id) ON DELETE SET NULL
);

-- =============================================
-- TABLE: payouts
-- =============================================
CREATE TABLE payouts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    owner_id INT UNSIGNED NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    commission DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    net_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending','processing','paid','rejected','failed') NOT NULL DEFAULT 'pending',
    requested_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    paid_at DATETIME DEFAULT NULL,
    -- Enforce at most 1 pending payout per owner (prevents duplicate withdrawals).
    pending_guard TINYINT(1) AS (CASE WHEN status = 'pending' THEN 1 ELSE 0 END) STORED,
    UNIQUE KEY uniq_owner_pending (owner_id, pending_guard),
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- TABLE: taxes
-- =============================================
CREATE TABLE taxes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    owner_id INT UNSIGNED NOT NULL,
    tax_year YEAR NOT NULL,
    total_revenue DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    tax_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    vat_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    generated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- TABLE: sensors
-- =============================================
CREATE TABLE sensors (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    spot_id INT UNSIGNED NOT NULL,
    sensor_code VARCHAR(50) NOT NULL,
    status ENUM('online','offline','error') NOT NULL DEFAULT 'online',
    last_ping DATETIME DEFAULT NULL,
    battery_level INT DEFAULT 100,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (spot_id) REFERENCES parking_spots(id) ON DELETE CASCADE
);

-- =============================================
-- TABLE: file_uploads
-- =============================================
CREATE TABLE file_uploads (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    file_type ENUM('evidence','owner_document','profile_image','report') NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    related_id INT UNSIGNED DEFAULT NULL,
    uploaded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- TABLE: favorites
-- =============================================
CREATE TABLE favorites (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    spot_id INT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (spot_id) REFERENCES parking_spots(id) ON DELETE CASCADE,
    UNIQUE KEY unique_fav (user_id, spot_id)
);

-- =============================================
-- SEED DATA (password for all demo users: password — bcrypt hash below)
-- =============================================

INSERT INTO users (name, email, password, phone, role, status) VALUES
('System Admin', 'admin@parking.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1000000001', 'admin', 'active'),
('John Driver', 'driver@parking.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1000000002', 'driver', 'active'),
('Alice Owner', 'owner@parking.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1000000003', 'owner', 'active'),
('Bob Officer', 'officer@parking.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1000000004', 'officer', 'active');

INSERT INTO parking_spots (owner_id, name, address, city, latitude, longitude, type, price_per_hour, total_slots, available_slots, ev_support, status, description) VALUES
(3, 'Downtown Parking A', '123 Main St', 'New York', 40.7128, -74.0060, 'public', 5.00, 10, 10, 1, 'active', 'Central parking near city hall'),
(3, 'Mall Parking B', '456 Elm Ave', 'New York', 40.7148, -74.0080, 'private', 3.50, 5, 5, 0, 'active', 'Shopping mall parking'),
(3, 'Harbor Spot C', '789 Harbor Rd', 'Brooklyn', 40.6892, -74.0445, 'reserved', 7.00, 3, 3, 1, 'active', 'Reserved harbor view spot');

INSERT INTO vehicles (user_id, plate_number, make, model, color, year, is_ev) VALUES
(2, 'ABC-1234', 'Toyota', 'Camry', 'White', 2020, 0),
(2, 'XYZ-9999', 'Tesla', 'Model 3', 'Black', 2022, 1);

-- Preferred vehicle for demo driver (first vehicle row = id 1)
UPDATE users SET default_vehicle_id = 1 WHERE id = 2;

ALTER TABLE users
    ADD CONSTRAINT fk_users_default_vehicle
    FOREIGN KEY (default_vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL;

INSERT INTO sensors (spot_id, sensor_code, status, last_ping, battery_level) VALUES
(1, 'SEN-001', 'online', NOW(), 95),
(1, 'SEN-002', 'online', NOW(), 88),
(2, 'SEN-003', 'offline', DATE_SUB(NOW(), INTERVAL 2 HOUR), 12),
(3, 'SEN-004', 'online', NOW(), 77);

INSERT INTO promo_codes (code, discount_percent, max_uses, valid_from, valid_until, status) VALUES
('WELCOME10', 10.00, 100, NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR), 'active'),
('SAVE20', 20.00, 50, NOW(), DATE_ADD(NOW(), INTERVAL 6 MONTH), 'active');

INSERT INTO notifications (user_id, title, message, type) VALUES
(2, 'Welcome!', 'Welcome to Smart Urban Parking. Book your first spot today.', 'system'),
(3, 'Welcome!', 'Welcome to Smart Urban Parking. List your first parking spot to start earning.', 'system');
-- =============================================
-- TABLE: emergency_reports (Priority 8)
-- =============================================
CREATE TABLE IF NOT EXISTS emergency_reports (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    report_type ENUM('accident','illegal_parking','safety_issue','blocked_spot','other') NOT NULL DEFAULT 'other',
    description TEXT NOT NULL,
    spot_id INT UNSIGNED DEFAULT NULL,
    reservation_id INT UNSIGNED DEFAULT NULL,
    status ENUM('open','in_progress','resolved') NOT NULL DEFAULT 'open',
    admin_note TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (spot_id) REFERENCES parking_spots(id) ON DELETE SET NULL,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE SET NULL
);

-- =============================================
-- BACKWARD-COMPATIBLE ALTER STATEMENTS
-- Run these on existing databases to apply Priority 8 and parking_spots update
-- =============================================
-- ALTER TABLE parking_spots MODIFY COLUMN status ENUM('active','inactive','maintenance','locked','owner-use') NOT NULL DEFAULT 'active';
-- CREATE TABLE IF NOT EXISTS emergency_reports (
--     id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
--     user_id INT UNSIGNED NOT NULL,
--     report_type ENUM('accident','illegal_parking','safety_issue','blocked_spot','other') NOT NULL DEFAULT 'other',
--     description TEXT NOT NULL,
--     spot_id INT UNSIGNED DEFAULT NULL,
--     reservation_id INT UNSIGNED DEFAULT NULL,
--     status ENUM('open','in_progress','resolved') NOT NULL DEFAULT 'open',
--     admin_note TEXT DEFAULT NULL,
--     created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
--     updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
--     FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
--     FOREIGN KEY (spot_id) REFERENCES parking_spots(id) ON DELETE SET NULL,
--     FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE SET NULL
-- );

-- =============================================
-- BACKWARD-COMPATIBLE ALTER STATEMENTS (Payout safety)
-- Run these on existing databases to prevent duplicate withdrawals
-- =============================================
-- ALTER TABLE payouts
--   MODIFY COLUMN status ENUM('pending','processing','paid','rejected','failed') NOT NULL DEFAULT 'pending';
-- ALTER TABLE payouts
--   ADD COLUMN pending_guard TINYINT(1) AS (CASE WHEN status = 'pending' THEN 1 ELSE 0 END) STORED,
--   ADD UNIQUE KEY uniq_owner_pending (owner_id, pending_guard);
