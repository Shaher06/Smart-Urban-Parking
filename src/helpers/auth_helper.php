<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function current_user(): array
{
    return $_SESSION['user'] ?? [];
}

function current_user_id(): int
{
    return (int)($_SESSION['user_id'] ?? 0);
}

function current_role(): string
{
    return $_SESSION['user']['role'] ?? '';
}

function is_admin(): bool
{
    return current_role() === 'admin';
}

function is_driver(): bool
{
    return current_role() === 'driver';
}

function is_owner(): bool
{
    return current_role() === 'owner';
}

function is_officer(): bool
{
    return current_role() === 'officer';
}

function login_user(array $user): void
{
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user']    = $user;
}

function logout_user(): void
{
    session_unset();
    session_destroy();
}