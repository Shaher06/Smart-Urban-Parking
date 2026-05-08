<?php
$role    = current_role();
$current = $_GET['page'] ?? '';
?>
<div class="col-md-2 sidebar bg-dark text-white min-vh-100 pt-3">
    <ul class="nav flex-column">
        <?php if ($role === 'driver'): ?>
            <li class="nav-item"><a class="nav-link text-white <?= $current === 'driver-dashboard' ? 'active' : '' ?>" href="<?= page_url('driver-dashboard') ?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('browse-spots') ?>"><i class="bi bi-search"></i> Browse Spots</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('nearby-spots') ?>"><i class="bi bi-geo"></i> Nearby Spots</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('reservations') ?>"><i class="bi bi-calendar-check"></i> Reservations</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('check-in-out') ?>"><i class="bi bi-qr-code"></i> Check In/Out</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('vehicles') ?>"><i class="bi bi-car-front"></i> Vehicles</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('fines') ?>"><i class="bi bi-exclamation-triangle"></i> Fines</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('favorites') ?>"><i class="bi bi-heart"></i> Favorites</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('waitlist') ?>"><i class="bi bi-hourglass"></i> Waitlist</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('driver-reviews') ?>"><i class="bi bi-star"></i> Reviews</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('driver-messages') ?>"><i class="bi bi-chat"></i> Messages</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('payment-history') ?>"><i class="bi bi-credit-card"></i> Payments</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('language') ?>"><i class="bi bi-translate"></i> Language</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('driver-emergency') ?>"><i class="bi bi-exclamation-octagon"></i> Emergency</a></li>
        <?php elseif ($role === 'owner'): ?>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('owner-dashboard') ?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('owner-spots') ?>"><i class="bi bi-geo-alt"></i> My Spots</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('add-spot') ?>"><i class="bi bi-plus-circle"></i> Add Spot</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('owner-earnings') ?>"><i class="bi bi-cash-stack"></i> Earnings</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('owner-payouts') ?>"><i class="bi bi-wallet2"></i> Payouts</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('owner-reviews') ?>"><i class="bi bi-star"></i> Reviews</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('owner-verification') ?>"><i class="bi bi-shield-check"></i> Verification</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('tax-details') ?>"><i class="bi bi-receipt-cutoff"></i> Tax Details</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('owner-messages') ?>"><i class="bi bi-chat"></i> Messages</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('owner-availability') ?>"><i class="bi bi-clock-history"></i> Availability</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('owner-pricing') ?>"><i class="bi bi-tag"></i> Pricing</a></li>
        <?php elseif ($role === 'admin' || $role === 'officer'): ?>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('admin-dashboard') ?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('admin-users') ?>"><i class="bi bi-people"></i> Users</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('admin-roles') ?>"><i class="bi bi-person-badge"></i> Roles</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('owners-verification') ?>"><i class="bi bi-patch-check"></i> Owner Verify</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('admin-fines') ?>"><i class="bi bi-receipt"></i> Fines</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('admin-appeals') ?>"><i class="bi bi-flag"></i> Appeals</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('admin-blacklist') ?>"><i class="bi bi-slash-circle"></i> Blacklist</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('event-zones') ?>"><i class="bi bi-lock"></i> Event Zones</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('emergency-override') ?>"><i class="bi bi-lightning"></i> Emergency Override</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('admin-emergency-reports') ?>"><i class="bi bi-exclamation-octagon"></i> Emergency Reports</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('officer-dispatch') ?>"><i class="bi bi-send"></i> Dispatch</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('system-health') ?>"><i class="bi bi-heart-pulse"></i> Health</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('audit-logs') ?>"><i class="bi bi-journal-text"></i> Audit Logs</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('admin-reports') ?>"><i class="bi bi-bar-chart"></i> Reports</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('heatmap') ?>"><i class="bi bi-fire"></i> Revenue heatmap</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= page_url('manage-payouts') ?>"><i class="bi bi-bank"></i> Payouts</a></li>
        <?php endif; ?>
    </ul>
</div>