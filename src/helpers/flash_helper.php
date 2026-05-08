<?php

function set_flash(string $type, string $message): void
{
    $_SESSION['_flash'][$type] = $message;
}

function get_flash(string $type): string
{
    $msg = $_SESSION['_flash'][$type] ?? '';
    unset($_SESSION['_flash'][$type]);
    return $msg;
}

function has_flash(string $type): bool
{
    return !empty($_SESSION['_flash'][$type]);
}

function render_flash(): string
{
    $html = '';
    foreach (['success', 'error', 'warning', 'info'] as $type) {
        $msg = get_flash($type);
        if ($msg !== '') {
            $cls  = match($type) {
                'success' => 'alert-success',
                'error'   => 'alert-danger',
                'warning' => 'alert-warning',
                default   => 'alert-info',
            };
            $safe  = htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
            $html .= "<div class=\"alert {$cls} alert-dismissible fade show\" role=\"alert\">"
                   . $safe
                   . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>'
                   . '</div>';
        }
    }
    return $html;
}