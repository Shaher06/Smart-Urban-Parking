<?php
require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/helpers/url_helper.php';
require_once BASE_PATH . '/helpers/flash_helper.php';
require_once BASE_PATH . '/helpers/auth_helper.php';
require_once BASE_PATH . '/helpers/report_helper.php';
require_once BASE_PATH . '/helpers/notification_helper.php';
$unread = is_logged_in() ? unread_notification_count(current_user_id()) : 0;
$lang   = 'en';
if (is_logged_in()) {
    $u = current_user();
    if (is_array($u) && !empty($u['language'])) {
        $lang = (string) $u['language'];
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <link rel="icon" href="<?= asset_url('img/favicon.svg') ?>" type="image/svg+xml">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= asset_url('css/style.css') ?>">
</head>
<body class="app-shell">
<main class="app-main">