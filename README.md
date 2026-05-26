# Smart Urban Parking Management System

A modern urban parking management web application built using native PHP and the MVC architectural pattern.  
The system provides a complete solution for managing parking spaces, reservations, vehicles, payments, pricing, fines, and notifications with role-based access control.

---

## Overview

Smart Urban Parking is designed to simplify parking management for drivers, parking owners, officers, and administrators through a centralized and scalable platform.

The project focuses on clean architecture, modular service design, and maintainable backend logic without relying on external PHP frameworks.

---

## Key Features

### Authentication & Authorization
- Secure login system
- Role-based access control
- Multiple user roles:
  - Admin
  - Driver
  - Parking Owner
  - Parking Officer

### Parking Management
- Add and manage parking spaces
- Parking availability tracking
- Slot status monitoring

### Booking System
- Reserve parking spaces
- Booking history management
- Vehicle-linked reservations

### Vehicle Management
- Register and manage vehicles
- Default vehicle selection support

### Payment System
- Modular payment service layer
- Extensible payment strategy implementation

### Pricing & Fines
- Dynamic parking pricing
- Fine calculation and management

### Notifications
- Driver notifications
- Booking and payment alerts

### Testing
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
