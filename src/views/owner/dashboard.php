<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-speedometer2"></i> Owner Dashboard</h3>
            <p class="text-muted">Welcome, <?= htmlspecialchars(current_user()['name']) ?></p>
            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <div class="card bg-primary text-white text-center p-3">
                        <i class="bi bi-geo-alt fs-2"></i>
                        <h4 class="mt-2"><?= $stats['totalSpots'] ?></h4>
                        <p>My Parking Spots</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white text-center p-3">
                        <i class="bi bi-cash-stack fs-2"></i>
                        <h4 class="mt-2">$<?= number_format($stats['totalRevenue'], 2) ?></h4>
                        <p>Total Earnings</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-dark text-center p-3">
                        <i class="bi bi-calendar-check fs-2"></i>
                        <h4 class="mt-2"><?= $stats['pendingBookings'] ?></h4>
                        <p>Confirmed Bookings</p>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Quick Actions</div>
                        <div class="card-body d-grid gap-2">
                            <a href="<?= page_url('owner-spots') ?>" class="btn btn-primary"><i class="bi bi-geo-alt"></i> Manage Spots</a>
                            <a href="<?= page_url('add-spot') ?>" class="btn btn-success"><i class="bi bi-plus-circle"></i> Add Spot</a>
                            <a href="<?= page_url('owner-earnings') ?>" class="btn btn-outline-success"><i class="bi bi-cash-stack"></i> View Earnings</a>
                            <a href="<?= page_url('owner-payouts') ?>" class="btn btn-outline-primary"><i class="bi bi-wallet2"></i> Request Payout</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Account</div>
                        <div class="card-body d-grid gap-2">
                            <a href="<?= page_url('owner-verification') ?>" class="btn btn-outline-secondary"><i class="bi bi-shield-check"></i> Verification Docs</a>
                            <a href="<?= page_url('owner-reviews') ?>" class="btn btn-outline-warning"><i class="bi bi-star"></i> View Reviews</a>
                            <a href="<?= page_url('tax-details') ?>" class="btn btn-outline-dark"><i class="bi bi-receipt-cutoff"></i> Tax Details</a>
                            <a href="<?= page_url('owner-messages') ?>" class="btn btn-outline-info"><i class="bi bi-chat"></i> Messages</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>