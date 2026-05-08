# Smart Urban Parking Management System

A native PHP MVC web application for managing urban parking spaces.

## Requirements
- PHP 7.4+
- MySQL 5.7+
- XAMPP / WAMP / LAMP

## Setup
1. Copy project to `C:\xampp\htdocs\Smart-Urban-Parking`
2. Import `database.sql` into MySQL
3. Edit `src/config/database.php` with your DB credentials
4. Visit: http://localhost/Smart-Urban-Parking/src/public/

## Default Login Credentials
All seed accounts use the same bcrypt hash (see `database.sql`). Password is **`password`**.

| Role    | Email                  | Password   |
|---------|------------------------|------------|
| Admin   | admin@parking.com      | password   |
| Driver  | driver@parking.com     | password   |
| Owner   | owner@parking.com      | password   |
| Officer | officer@parking.com    | password   |

After import, the demo driver has `default_vehicle_id` set to the first seeded vehicle for booking convenience.

## Tech Stack
- Native PHP (no frameworks)
- MySQL + PDO
- Bootstrap 5 CDN
- MVC Architecture

## Design Patterns Used
1. MVC Pattern
2. Strategy Pattern (PaymentService interface)
3. Service Layer Pattern