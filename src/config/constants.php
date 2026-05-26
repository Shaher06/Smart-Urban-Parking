<?php
/**
 * APPLICATION CONSTANTS

 */

// ── Roles ─────────────────────────────────────────────────────────────────────
if (!defined('ROLE_DRIVER'))  define('ROLE_DRIVER',  'driver');
if (!defined('ROLE_OWNER'))   define('ROLE_OWNER',   'owner');
if (!defined('ROLE_ADMIN'))   define('ROLE_ADMIN',   'admin');
if (!defined('ROLE_OFFICER')) define('ROLE_OFFICER', 'officer');

// ── User Status ───────────────────────────────────────────────────────────────
if (!defined('STATUS_ACTIVE'))      define('STATUS_ACTIVE',      'active');
if (!defined('STATUS_SUSPENDED'))   define('STATUS_SUSPENDED',   'suspended');
if (!defined('STATUS_BLACKLISTED')) define('STATUS_BLACKLISTED', 'blacklisted');

// ── Reservation Status ────────────────────────────────────────────────────────
if (!defined('RES_PENDING'))   define('RES_PENDING',   'pending');
if (!defined('RES_CONFIRMED')) define('RES_CONFIRMED', 'confirmed');
if (!defined('RES_ACTIVE'))    define('RES_ACTIVE',    'active');
if (!defined('RES_COMPLETED')) define('RES_COMPLETED', 'completed');
if (!defined('RES_CANCELLED')) define('RES_CANCELLED', 'cancelled');

// ── Payment Status — Lifecycle: pending → escrow → completed ─────────────────
// Or: pending → failed   |   completed → refunded
if (!defined('PAY_PENDING'))   define('PAY_PENDING',   'pending');
if (!defined('PAY_ESCROW'))    define('PAY_ESCROW',    'escrow');
if (!defined('PAY_COMPLETED')) define('PAY_COMPLETED', 'completed');
if (!defined('PAY_FAILED'))    define('PAY_FAILED',    'failed');
if (!defined('PAY_REFUNDED'))  define('PAY_REFUNDED',  'refunded');

// ── Fine Status ───────────────────────────────────────────────────────────────
if (!defined('FINE_UNPAID'))   define('FINE_UNPAID',   'unpaid');
if (!defined('FINE_PAID'))     define('FINE_PAID',     'paid');
if (!defined('FINE_APPEALED')) define('FINE_APPEALED', 'appealed');
if (!defined('FINE_WAIVED'))   define('FINE_WAIVED',   'waived');

// ── Appeal Status ─────────────────────────────────────────────────────────────
if (!defined('APPEAL_PENDING'))  define('APPEAL_PENDING',  'pending');
if (!defined('APPEAL_APPROVED')) define('APPEAL_APPROVED', 'approved');
if (!defined('APPEAL_REJECTED')) define('APPEAL_REJECTED', 'rejected');

// ── Marketplace / Payouts ─────────────────────────────────────────────────────
// Minimum net amount allowed per payout request.
// Keep low by default; adjust per business requirements.
if (!defined('MIN_PAYOUT_NET')) define('MIN_PAYOUT_NET', 1.00);