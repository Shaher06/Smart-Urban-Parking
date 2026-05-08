<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-heart-pulse"></i> System Health Monitor</h3>
            <div class="row g-3 mb-4">
                <div class="col-md-2"><div class="card bg-success text-white text-center p-3"><i class="bi bi-database fs-2"></i><p class="mt-2"><?= htmlspecialchars($health['db_status']) ?></p><small>Database</small></div></div>
                <div class="col-md-2"><div class="card bg-primary text-white text-center p-3"><i class="bi bi-people fs-2"></i><h5 class="mt-2"><?= $health['total_users'] ?></h5><small>Users</small></div></div>
                <div class="col-md-2"><div class="card bg-info text-white text-center p-3"><i class="bi bi-geo-alt fs-2"></i><h5 class="mt-2"><?= $health['active_spots'] ?></h5><small>Active Spots</small></div></div>
                <div class="col-md-2"><div class="card bg-warning text-dark text-center p-3"><i class="bi bi-calendar fs-2"></i><h5 class="mt-2"><?= $health['today_bookings'] ?></h5><small>Today's Bookings</small></div></div>
                <div class="col-md-4"><div class="card text-center p-3">
                    <small class="text-muted">Server Time: <?= $health['server_time'] ?></small><br>
                    <small class="text-muted">PHP: <?= $health['php_version'] ?></small>
                </div></div>
            </div>
            <h5>Sensor Status</h5>
            <div class="row g-2 mb-3">
                <div class="col-md-3"><div class="card border-success text-center p-2"><strong><?= $health['sensor_stats']['online'] ?? 0 ?></strong><br><small class="text-success">Online</small></div></div>
                <div class="col-md-3"><div class="card border-danger text-center p-2"><strong><?= $health['sensor_stats']['offline'] ?? 0 ?></strong><br><small class="text-danger">Offline</small></div></div>
                <div class="col-md-3"><div class="card border-warning text-center p-2"><strong><?= $health['sensor_stats']['error_count'] ?? 0 ?></strong><br><small class="text-warning">Error</small></div></div>
                <div class="col-md-3"><div class="card border-primary text-center p-2"><strong><?= $health['sensor_stats']['total'] ?? 0 ?></strong><br><small class="text-primary">Total</small></div></div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead class="table-dark"><tr><th>Code</th><th>Spot</th><th>City</th><th>Status</th><th>Last Ping</th><th>Battery</th></tr></thead>
                    <tbody>
                        <?php foreach ($health['sensors'] as $s): ?>
                            <tr>
                                <td class="font-monospace"><?= htmlspecialchars($s['sensor_code']) ?></td>
                                <td><?= htmlspecialchars($s['spot_name']) ?></td>
                                <td><?= htmlspecialchars($s['city']) ?></td>
                                <td><?= status_badge($s['status']) ?></td>
                                <td><?= $s['last_ping'] ? htmlspecialchars($s['last_ping']) : 'Never' ?></td>
                                <td>
                                    <div class="progress" style="height:15px;width:80px">
                                        <div class="progress-bar bg-<?= $s['battery_level'] > 50 ? 'success' : ($s['battery_level'] > 20 ? 'warning' : 'danger') ?>" style="width:<?= $s['battery_level'] ?>%"><?= $s['battery_level'] ?>%</div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>