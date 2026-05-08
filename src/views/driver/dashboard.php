<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-speedometer2"></i> Driver Dashboard</h3>
            <p class="text-muted">Welcome back, <?= htmlspecialchars(current_user()['name']) ?>!</p>

            <div class="row g-3 mt-2">
                <div class="col-md-3">
                    <div class="card bg-primary text-white text-center p-3">
                        <i class="bi bi-calendar-check fs-2"></i>
                        <h4 class="mt-2"><?= $stats['totalReservations'] ?></h4>
                        <p>Total Reservations</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-dark text-center p-3">
                        <i class="bi bi-exclamation-triangle fs-2"></i>
                        <h4 class="mt-2"><?= $stats['unpaidFines'] ?></h4>
                        <p>Unpaid Fines</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white text-center p-3">
                        <i class="bi bi-car-front fs-2"></i>
                        <h4 class="mt-2"><?= $stats['vehicles'] ?></h4>
                        <p>My Vehicles</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white text-center p-3">
                        <i class="bi bi-heart fs-2"></i>
                        <h4 class="mt-2"><?= $stats['favorites'] ?></h4>
                        <p>Favorites</p>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Quick Actions</div>
                        <div class="card-body d-grid gap-2">
                            <a href="<?= page_url('browse-spots') ?>" class="btn btn-primary"><i class="bi bi-search"></i> Browse Parking Spots</a>
                            <a href="<?= page_url('reservations') ?>" class="btn btn-outline-primary"><i class="bi bi-calendar"></i> My Reservations</a>
                            <a href="<?= page_url('check-in-out') ?>" class="btn btn-outline-success"><i class="bi bi-qr-code-scan"></i> Check In / Out</a>
                            <a href="<?= page_url('fines') ?>" class="btn btn-outline-danger"><i class="bi bi-exclamation"></i> View Fines</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">My Vehicles</div>
                        <div class="card-body">
                            <a href="<?= page_url('vehicles') ?>" class="btn btn-sm btn-success mb-2"><i class="bi bi-plus"></i> Manage Vehicles</a>
                            <a href="<?= page_url('waitlist') ?>" class="btn btn-sm btn-warning mb-2"><i class="bi bi-hourglass"></i> Waitlist</a>
                            <a href="<?= page_url('favorites') ?>" class="btn btn-sm btn-info mb-2"><i class="bi bi-heart"></i> Favorites</a>
                            <a href="<?= page_url('notifications') ?>" class="btn btn-sm btn-secondary mb-2"><i class="bi bi-bell"></i> Notifications</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>