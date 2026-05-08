<?php
require_once BASE_PATH . '/helpers/auth_helper.php';
require_once BASE_PATH . '/helpers/url_helper.php';
require_once BASE_PATH . '/helpers/notification_helper.php';
$unread = is_logged_in() ? unread_notification_count(current_user_id()) : 0;
$role   = current_role();
$homePage = 'home';
if (is_logged_in()) {
    $homePage = match ($role) {
        'admin', 'officer' => 'admin-dashboard',
        'owner'            => 'owner-dashboard',
        default            => 'driver-dashboard',
    };
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?= page_url($homePage) ?>">
            <i class="bi bi-p-circle-fill"></i> <?= APP_NAME ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <?php if (is_logged_in()): ?>
                    <?php if ($role === 'driver'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= page_url('browse-spots') ?>"><i class="bi bi-search"></i> Browse</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= page_url('reservations') ?>"><i class="bi bi-calendar-check"></i> Reservations</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= page_url('vehicles') ?>"><i class="bi bi-car-front"></i> Vehicles</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= page_url('fines') ?>"><i class="bi bi-exclamation-triangle"></i> Fines</a></li>
                    <?php elseif ($role === 'owner'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= page_url('owner-spots') ?>"><i class="bi bi-geo-alt"></i> My Spots</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= page_url('owner-earnings') ?>"><i class="bi bi-cash-stack"></i> Earnings</a></li>
                    <?php elseif ($role === 'admin' || $role === 'officer'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= page_url('admin-users') ?>"><i class="bi bi-people"></i> Users</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= page_url('admin-fines') ?>"><i class="bi bi-receipt"></i> Fines</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= page_url('admin-reports') ?>"><i class="bi bi-bar-chart"></i> Reports</a></li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="<?= page_url('notifications') ?>">
                            <i class="bi bi-bell"></i>
                            <?php if ($unread > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $unread ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?= htmlspecialchars(current_user()['name'] ?? '') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= page_url('profile') ?>"><i class="bi bi-person"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="<?= page_url('payment-history') ?>"><i class="bi bi-credit-card"></i> Payments</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= page_url('logout') ?>"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= page_url('home') ?>">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= page_url('login') ?>">Login</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-light text-primary ms-2 px-3" href="<?= page_url('register') ?>">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>