<?php

function format_currency(float $amount, string $symbol = '$'): string
{
    return $symbol . number_format($amount, 2);
}

function format_date(string $datetime): string
{
    return date('M d, Y H:i', strtotime($datetime));
}

function format_date_short(string $datetime): string
{
    return date('M d, Y', strtotime($datetime));
}

function calculate_duration(string $start, string $end): string
{
    $diff = strtotime($end) - strtotime($start);
    $hours   = floor($diff / 3600);
    $minutes = floor(($diff % 3600) / 60);
    return "{$hours}h {$minutes}m";
}

function status_badge(string $status): string
{
    $map = [
        'active'      => 'success',
        'confirmed'   => 'success',
        'completed'   => 'primary',
        'pending'     => 'warning',
        'waiting'     => 'warning',
        'cancelled'   => 'secondary',
        'failed'      => 'danger',
        'unpaid'      => 'danger',
        'paid'        => 'success',
        'appealed'    => 'info',
        'approved'    => 'success',
        'rejected'    => 'danger',
        'suspended'   => 'warning',
        'blacklisted' => 'danger',
        'online'      => 'success',
        'offline'     => 'danger',
        'error'       => 'warning',
        'locked'      => 'danger',
        'inactive'    => 'secondary',
        'maintenance' => 'warning',
        'owner-use'   => 'info',
        'escrow'      => 'info',
        'refunded'    => 'secondary',
        'no_show'     => 'dark',
        'notified'    => 'info',
        'waived'      => 'success',
        'processing'  => 'info',
        'maintenance' => 'warning',
        'inactive'    => 'secondary',
    ];
    $color = $map[$status] ?? 'secondary';
    $safe  = htmlspecialchars(ucfirst($status), ENT_QUOTES, 'UTF-8');
    return "<span class=\"badge bg-{$color}\">{$safe}</span>";
}