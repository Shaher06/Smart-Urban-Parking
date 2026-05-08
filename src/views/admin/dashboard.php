<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>

            <h3><i class="bi bi-speedometer2"></i> Admin Dashboard</h3>
            <p class="text-muted"><?= date('l, F j, Y — H:i') ?></p>

            <!-- ── System Stats ─────────────────────────────────────────────── -->
            <div class="row g-3 mb-4">
                <div class="col-md-2">
                    <div class="card bg-primary text-white text-center p-3">
                        <i class="bi bi-people fs-2"></i>
                        <h4 class="mt-1"><?= $stats['users'] ?></h4>
                        <small>Total Users</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-success text-white text-center p-3">
                        <i class="bi bi-geo-alt fs-2"></i>
                        <h4 class="mt-1"><?= $stats['spots'] ?></h4>
                        <small>Active Spots</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-info text-white text-center p-3">
                        <i class="bi bi-calendar-check fs-2"></i>
                        <h4 class="mt-1"><?= $stats['reservations'] ?></h4>
                        <small>Reservations</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-warning text-dark text-center p-3">
                        <i class="bi bi-cash-stack fs-2"></i>
                        <h4 class="mt-1">$<?= number_format($stats['totalRevenue'], 0) ?></h4>
                        <small>Revenue</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-danger text-white text-center p-3">
                        <i class="bi bi-exclamation-triangle fs-2"></i>
                        <h4 class="mt-1"><?= $fine_stats['unpaid'] ?></h4>
                        <small>Unpaid Fines</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-secondary text-white text-center p-3">
                        <i class="bi bi-flag fs-2"></i>
                        <h4 class="mt-1"><?= $stats['appeals'] ?></h4>
                        <small>Pending Appeals</small>
                    </div>
                </div>
            </div>

            <!-- ── Fine Stats ───────────────────────────────────────────────── -->
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <span><i class="bi bi-receipt"></i> Fine Summary</span>
                            <a href="<?= page_url('admin-fines') ?>" class="btn btn-sm btn-outline-danger">Manage Fines</a>
                        </div>
                        <div class="card-body">
                            <div class="row text-center g-2">
                                <div class="col"><strong><?= $fine_stats['total'] ?></strong><br><small class="text-muted">Total</small></div>
                                <div class="col text-danger"><strong><?= $fine_stats['unpaid'] ?></strong><br><small class="text-muted">Unpaid</small></div>
                                <div class="col text-success"><strong><?= $fine_stats['paid'] ?></strong><br><small class="text-muted">Paid</small></div>
                                <div class="col text-secondary"><strong><?= $fine_stats['waived'] ?></strong><br><small class="text-muted">Waived</small></div>
                                <div class="col text-success"><strong>$<?= number_format($fine_stats['revenue'], 2) ?></strong><br><small class="text-muted">Fine Revenue</small></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Live Occupancy ────────────────────────────────────────────── -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-activity"></i> Live Spot Occupancy</span>
                    <a href="<?= page_url('occupancy') ?>" class="btn btn-sm btn-outline-info">Full Report</a>
                </div>
                <div class="card-body">
                    <?php if (empty($live_occupancy)): ?>
                        <p class="text-muted">No occupancy data available.</p>
                    <?php else: ?>
                        <div class="row g-2">
                            <?php foreach (array_slice($live_occupancy, 0, 6) as $spot): ?>
                                <?php
                                    $pct   = (float)$spot['occupancy_pct'];
                                    $color = $pct >= 80 ? 'danger' : ($pct >= 50 ? 'warning' : 'success');
                                ?>
                                <div class="col-md-4">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span><?= htmlspecialchars($spot['name']) ?></span>
                                        <span class="text-<?= $color ?>"><?= $pct ?>%</span>
                                    </div>
                                    <div class="progress" style="height:10px">
                                        <div class="progress-bar bg-<?= $color ?>" style="width:<?= $pct ?>%"></div>
                                    </div>
                                    <small class="text-muted"><?= $spot['available_slots'] ?>/<?= $spot['total_slots'] ?> free</small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ── Quick Actions ─────────────────────────────────────────────── -->
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-header">Users</div>
                        <div class="card-body d-grid gap-2">
                            <a href="<?= page_url('admin-users') ?>" class="btn btn-primary btn-sm">Manage Users</a>
                            <a href="<?= page_url('admin-roles') ?>" class="btn btn-outline-primary btn-sm">Manage Roles</a>
                            <a href="<?= page_url('admin-blacklist') ?>" class="btn btn-outline-danger btn-sm">Blacklist</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-header">Enforcement</div>
                        <div class="card-body d-grid gap-2">
                            <a href="<?= page_url('admin-fines') ?>" class="btn btn-danger btn-sm">Issue / Manage Fines</a>
                            <a href="<?= page_url('admin-appeals') ?>" class="btn btn-warning btn-sm">Review Appeals</a>
                            <a href="<?= page_url('officer-dispatch') ?>" class="btn btn-outline-secondary btn-sm">Dispatch Officer</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-header">System</div>
                        <div class="card-body d-grid gap-2">
                            <a href="<?= page_url('system-health') ?>" class="btn btn-info text-white btn-sm">System Health</a>
                            <a href="<?= page_url('audit-logs') ?>" class="btn btn-outline-info btn-sm">Audit Logs</a>
                            <a href="<?= page_url('emergency-override') ?>" class="btn btn-outline-danger btn-sm">Emergency</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-header">Reports</div>
                        <div class="card-body d-grid gap-2">
                            <a href="<?= page_url('admin-reports') ?>" class="btn btn-outline-primary btn-sm">Revenue Report</a>
                            <a href="<?= page_url('heatmap') ?>" class="btn btn-outline-success btn-sm">Heatmap</a>
                            <a href="<?= page_url('export-pdf', ['type' => 'revenue']) ?>" class="btn btn-outline-dark btn-sm">Export PDF</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>