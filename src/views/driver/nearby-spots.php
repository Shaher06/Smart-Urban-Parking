<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-geo"></i> Nearby Parking Spots</h3>
            <form method="GET" class="card p-3 mb-4">
                <input type="hidden" name="page" value="nearby-spots">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Latitude</label>
                        <input type="number" name="lat" step="any" class="form-control" value="<?= htmlspecialchars($lat) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Longitude</label>
                        <input type="number" name="lng" step="any" class="form-control" value="<?= htmlspecialchars($lng) ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100 mt-4"><i class="bi bi-search"></i> Find Nearby</button>
                    </div>
                </div>
            </form>
            <?php if (empty($spots)): ?>
                <div class="alert alert-info">No nearby spots found.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark"><tr><th>Name</th><th>City</th><th>Distance (km)</th><th>Price/hr</th><th>Slots</th><th>Action</th></tr></thead>
                        <tbody>
                            <?php foreach ($spots as $s): ?>
                                <tr>
                                    <td><?= htmlspecialchars($s['name']) ?></td>
                                    <td><?= htmlspecialchars($s['city']) ?></td>
                                    <td><?= number_format($s['distance'] ?? 0, 2) ?> km</td>
                                    <td>$<?= number_format($s['price_per_hour'], 2) ?></td>
                                    <td><?= $s['available_slots'] ?></td>
                                    <td><a href="<?= page_url('book-spot', ['id' => $s['id']]) ?>" class="btn btn-sm btn-primary">Book</a></td>
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