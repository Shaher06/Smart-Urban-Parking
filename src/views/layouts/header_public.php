<?php
require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/helpers/url_helper.php';
require_once BASE_PATH . '/helpers/flash_helper.php';
require_once BASE_PATH . '/helpers/auth_helper.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <link rel="icon" href="<?= asset_url('img/favicon.svg') ?>" type="image/svg+xml">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= asset_url('css/style.css') ?>">
</head>
<body class="app-shell public-landing">
<main class="app-main">

