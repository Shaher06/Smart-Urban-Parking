<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-tag"></i> Pricing Management</h3>
            <p class="text-muted">Peak-hour multiplier (1.5×) still applies at booking time (7–9am, 5–7pm). Suggested rates blend your city average with occupancy.</p>

            <?php if (empty($spots)): ?>
                <div class="alert alert-info">Add a spot first, then set prices here.</div>
            <?php else: ?>
                <div class="table-responsive card shadow-sm">
                    <table class="table table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Spot</th>
                                <th>City</th>
                                <th>Current $/hr</th>
                                <th>Suggested $/hr</th>
                                <th>Update</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($spots as $s): ?>
                                <?php $sid = (int)$s['id']; $sug = $suggested[$sid] ?? (float)$s['price_per_hour']; ?>
                                <tr>
                                    <td><?= htmlspecialchars($s['name']) ?></td>
                                    <td><?= htmlspecialchars($s['city']) ?></td>
                                    <td>$<?= number_format((float)$s['price_per_hour'], 2) ?></td>
                                    <td><span class="badge bg-secondary">$<?= number_format($sug, 2) ?></span></td>
                                    <td>
                                        <form method="POST" class="d-flex gap-2 align-items-center">
                                            <input type="hidden" name="spot_id" value="<?= $sid ?>">
                                            <input type="number" name="price_per_hour" class="form-control form-control-sm" style="max-width:110px" step="0.01" min="0.5" value="<?= htmlspecialchars((string)$sug) ?>">
                                            <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
