<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container mt-4">
    <?= render_flash() ?>
    <h3><i class="bi bi-grid-3x3"></i> Revenue Heatmap</h3>
    <p class="text-muted">Simulated visual revenue heatmap by parking spot.</p>
    <div class="row g-2">
        <?php foreach ($bySpot as $s):
            $max  = max(array_column($bySpot, 'revenue')) ?: 1;
            $pct  = ($s['revenue'] / $max) * 100;
            $heat = $pct > 80 ? 'danger' : ($pct > 50 ? 'warning' : ($pct > 20 ? 'info' : 'secondary'));
        ?>
            <div class="col-md-3">
                <div class="card border-<?= $heat ?> text-center p-3">
                    <h6><?= htmlspecialchars($s['spot_name']) ?></h6>
                    <p class="text-muted small"><?= htmlspecialchars($s['city']) ?></p>
                    <div class="progress mb-2"><div class="progress-bar bg-<?= $heat ?>" style="width:<?= round($pct) ?>%"><?= round($pct) ?>%</div></div>
                    <strong class="text-<?= $heat ?>">$<?= number_format($s['revenue'], 2) ?></strong><br>
                    <small><?= $s['total_reservations'] ?> bookings</small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>