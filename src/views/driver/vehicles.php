<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-car-front"></i> My Vehicles</h3>

            <?php if (!empty($edit_vehicle)): ?>
            <div class="card mb-4 border-warning">
                <div class="card-header bg-warning">Edit Vehicle</div>
                <div class="card-body">
                    <form method="POST" action="<?= page_url('edit-vehicle', ['id' => $edit_vehicle['id']]) ?>">
                        <div class="row g-2">
                            <div class="col-md-3"><input type="text" name="plate_number" class="form-control" value="<?= htmlspecialchars($edit_vehicle['plate_number']) ?>" placeholder="Plate Number" required></div>
                            <div class="col-md-2"><input type="text" name="make" class="form-control" value="<?= htmlspecialchars($edit_vehicle['make'] ?? '') ?>" placeholder="Make"></div>
                            <div class="col-md-2"><input type="text" name="model" class="form-control" value="<?= htmlspecialchars($edit_vehicle['model'] ?? '') ?>" placeholder="Model"></div>
                            <div class="col-md-2"><input type="text" name="color" class="form-control" value="<?= htmlspecialchars($edit_vehicle['color'] ?? '') ?>" placeholder="Color"></div>
                            <div class="col-md-1"><input type="number" name="year" class="form-control" value="<?= htmlspecialchars($edit_vehicle['year'] ?? '') ?>" placeholder="Year"></div>
                            <div class="col-md-1 d-flex align-items-center"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_ev" id="ev_edit" <?= $edit_vehicle['is_ev'] ? 'checked' : '' ?>><label class="form-check-label" for="ev_edit">EV</label></div></div>
                            <div class="col-md-1"><button type="submit" class="btn btn-warning w-100">Update</button></div>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-header bg-success text-white">Add New Vehicle</div>
                <div class="card-body">
                    <form method="POST" action="<?= page_url('add-vehicle') ?>">
                        <div class="row g-2">
                            <div class="col-md-3"><input type="text" name="plate_number" class="form-control" placeholder="Plate Number *" required></div>
                            <div class="col-md-2"><input type="text" name="make" class="form-control" placeholder="Make"></div>
                            <div class="col-md-2"><input type="text" name="model" class="form-control" placeholder="Model"></div>
                            <div class="col-md-2"><input type="text" name="color" class="form-control" placeholder="Color"></div>
                            <div class="col-md-1"><input type="number" name="year" class="form-control" placeholder="Year" min="1990" max="2030"></div>
                            <div class="col-md-1 d-flex align-items-center"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_ev" id="ev_add"><label class="form-check-label" for="ev_add">EV</label></div></div>
                            <div class="col-md-1"><button type="submit" class="btn btn-success w-100"><i class="bi bi-plus"></i> Add</button></div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (empty($vehicles)): ?>
                <div class="alert alert-info">No vehicles registered yet.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark"><tr><th>Plate</th><th>Make</th><th>Model</th><th>Color</th><th>Year</th><th>EV</th><th>Default</th><th>Actions</th></tr></thead>
                        <tbody>
                            <?php foreach ($vehicles as $v): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($v['plate_number']) ?></strong></td>
                                    <td><?= htmlspecialchars($v['make'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($v['model'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($v['color'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($v['year'] ?? 'N/A') ?></td>
                                    <td><?= $v['is_ev'] ? '<span class="badge bg-success"><i class="bi bi-lightning"></i> Yes</span>' : 'No' ?></td>
                                    <td><?php $dv = (int)($default_vehicle_id ?? 0); ?>
                                        <?php if ($dv === (int)$v['id']): ?>
                                            <span class="badge bg-primary">Default</span>
                                        <?php else: ?>
                                            <a href="<?= page_url('set-default-vehicle', ['id' => $v['id']]) ?>" class="btn btn-outline-primary btn-sm">Set default</a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= page_url('edit-vehicle', ['id' => $v['id']]) ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="<?= page_url('delete-vehicle', ['id' => $v['id']]) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this vehicle?')">Delete</a>
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