<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container mt-4">
    <?= render_flash() ?>
    <h3><i class="bi bi-graph-up-arrow"></i> Revenue Report</h3>
    <div class="alert alert-success fw-bold">Total Revenue: $<?= number_format($total, 2) ?></div>
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3"><div class="card-header">Monthly Revenue</div><div class="card-body">
                <table class="table table-sm"><thead class="table-dark"><tr><th>Month</th><th>Revenue</th></tr></thead><tbody>
                    <?php foreach ($monthly as $m): ?><tr><td><?= htmlspecialchars($m['month']) ?></td><td>$<?= number_format($m['total'], 2) ?></td></tr><?php endforeach; ?>
                </tbody></table>
            </div></div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3"><div class="card-header">By Spot</div><div class="card-body">
                <table class="table table-sm"><thead class="table-dark"><tr><th>Spot</th><th>Bookings</th><th>Revenue</th></tr></thead><tbody>
                    <?php foreach ($bySpot as $s): ?><tr><td><?= htmlspecialchars($s['spot_name']) ?></td><td><?= $s['total_reservations'] ?></td><td>$<?= number_format($s['revenue'], 2) ?></td></tr><?php endforeach; ?>
                </tbody></table>
            </div></div>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>