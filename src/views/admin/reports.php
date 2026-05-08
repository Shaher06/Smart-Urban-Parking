<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-bar-chart"></i> Reports</h3>
            <div class="alert alert-success fw-bold">Total System Revenue: $<?= number_format($total, 2) ?></div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">Monthly Revenue</div>
                        <div class="card-body">
                            <table class="table table-sm table-striped">
                                <thead class="table-dark"><tr><th>Month</th><th>Revenue ($)</th></tr></thead>
                                <tbody>
                                    <?php foreach ($monthly as $m): ?>
                                        <tr><td><?= htmlspecialchars($m['month']) ?></td><td>$<?= number_format($m['total'], 2) ?></td></tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">Revenue by Spot</div>
                        <div class="card-body">
                            <table class="table table-sm table-striped">
                                <thead class="table-dark"><tr><th>Spot</th><th>City</th><th>Bookings</th><th>Revenue ($)</th></tr></thead>
                                <tbody>
                                    <?php foreach ($bySpot as $s): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($s['spot_name']) ?></td>
                                            <td><?= htmlspecialchars($s['city']) ?></td>
                                            <td><?= $s['total_reservations'] ?></td>
                                            <td>$<?= number_format($s['revenue'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= page_url('heatmap') ?>" class="btn btn-outline-primary"><i class="bi bi-grid-3x3"></i> Revenue Heatmap</a>
                <a href="<?= page_url('revenue') ?>" class="btn btn-outline-success"><i class="bi bi-graph-up"></i> Detailed Revenue</a>
            </div>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>