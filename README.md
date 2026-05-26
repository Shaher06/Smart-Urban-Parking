# Smart Urban Parking Management System

A modern urban parking management web application built using native PHP and the MVC architectural pattern.  
The system provides a complete solution for managing parking spaces, reservations, vehicles, payments, pricing, fines, and notifications with role-based access control.

---

# Overview

Smart Urban Parking is designed to simplify parking management for drivers, parking owners, officers, and administrators through a centralized and scalable platform.

The project focuses on clean architecture, modular service design, and maintainable backend logic without relying on external PHP frameworks.

---

# Key Features

## Authentication & Authorization
- Secure login system
- Role-based access control
- Multiple user roles:
  - Admin
  - Driver
  - Parking Owner
  - Parking Officer

## Parking Management
- Add and manage parking spaces
- Parking availability tracking
- Slot status monitoring

## Booking System
- Reserve parking spaces
- Booking history management
- Vehicle-linked reservations

## Vehicle Management
- Register and manage vehicles
- Default vehicle selection support

## Payment System
- Modular payment service layer
- Extensible payment strategy implementation

## Pricing & Fines
- Dynamic parking pricing
- Fine calculation and management

## Notifications
- Driver notifications
- Booking and payment alerts

## Testing
- White-box testing for service classes
- Unit testing for pricing, payments, notifications, and fines

---

# Tech Stack

| Technology | Usage |
|---|---|
| PHP 7.4+ | Backend Development |
| MySQL | Database |
| PDO | Database Access |
| Bootstrap 5 | Frontend UI |
| HTML/CSS | User Interface |
| Docker | Containerization |
| MVC Architecture | Project Structure |

---

# Project Structure

```text
Smart-Urban-Parking/
│
├── src/
│   ├── config/
│   ├── controllers/
│   ├── core/
│   ├── helpers/
│   ├── interfaces/
│   ├── models/
│   ├── public/
│   ├── services/
│   └── views/
│
├── tests/
│   └── white-box/
│
├── docs/
│   └── diagrams/
│
├── database.sql
├── Dockerfile
└── README.md
```

---

# Design Patterns Used

- MVC Pattern
- Service Layer Pattern
- Strategy Pattern
- Interface-Based Architecture

---

# Installation Guide

## 1. Clone Repository

```bash
git clone https://github.com/Shaher06/Smart-Urban-Parking.git
```

---

## 2. Move Project to XAMPP

```text
C:\xampp\htdocs\Smart-Urban-Parking
```

---

## 3. Import Database

Import:

```text
database.sql
```

into MySQL using phpMyAdmin.

---

## 4. Configure Database

Edit:

```text
src/config/database.php
```

and update your database credentials.

---

## 5. Start Server

Run:
- Apache
- MySQL

from XAMPP Control Panel.

---

## 6. Open Application

```text
http://localhost/Smart-Urban-Parking/src/public/
```

---

# Default Demo Accounts

All demo accounts use:

```text
Password: password
```

| Role | Email |
|---|---|
| Admin | admin@parking.com |
| Driver | driver@parking.com |
| Owner | owner@parking.com |
| Officer | officer@parking.com |

---

# Testing

White-box tests are included for core services:

```text
tests/white-box/FineServiceTest.php
tests/white-box/NotificationServiceTest.php
tests/white-box/PaymentServiceTest.php
tests/white-box/PricingServiceTest.php
```

---

# Documentation

System diagrams and documentation are available in:

```text
docs/diagrams/
```

---

# Team Members

## Team Leader
- Shaher Mamdouh

## Team Members
- Tarek Hany
- Alaa Tarek
- Sabreen Walid
- Malak Mohamed
- Seif Eldin Tamer
- Omar Mohamed

---

# License

This project was developed for educational and academic purposes.

---

# Repository

```text
https://github.com/Shaher06/Smart-Urban-Parking
```
