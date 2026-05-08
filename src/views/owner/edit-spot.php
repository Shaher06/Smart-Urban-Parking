<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-pencil"></i> Edit Parking Spot</h3>
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Spot Name *</label><input type="text" name="name" class="form-control" value="<?= htmlspecialchars($spot['name']) ?>" required></div>
                            <div class="col-md-6"><label class="form-label">Address *</label><input type="text" name="address" class="form-control" value="<?= htmlspecialchars($spot['address']) ?>" required></div>
                            <div class="col-md-4"><label class="form-label">City *</label><input type="text" name="city" class="form-control" value="<?= htmlspecialchars($spot['city']) ?>" required></div>
                            <div class="col-md-4"><label class="form-label">Type</label>
                                <select name="type" class="form-select">
                                    <?php foreach (['public','private','reserved'] as $t): ?>
                                        <option value="<?= $t ?>" <?= $spot['type'] === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4"><label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <?php foreach (['active','inactive','maintenance','locked'] as $st): ?>
                                        <option value="<?= $st ?>" <?= $spot['status'] === $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3"><label class="form-label">Price/hr ($) *</label><input type="number" name="price_per_hour" class="form-control" step="0.01" value="<?= $spot['price_per_hour'] ?>" required></div>
                            <div class="col-md-2"><label class="form-label">Total Slots</label><input type="number" name="total_slots" class="form-control" value="<?= $spot['total_slots'] ?>"></div>
                            <div class="col-md-2"><label class="form-label">Available</label><input type="number" name="available_slots" class="form-control" value="<?= $spot['available_slots'] ?>"></div>
                            <div class="col-md-2"><label class="form-label">Latitude</label><input type="number" name="latitude" class="form-control" step="any" value="<?= $spot['latitude'] ?>"></div>
                            <div class="col-md-2"><label class="form-label">Longitude</label><input type="number" name="longitude" class="form-control" step="any" value="<?= $spot['longitude'] ?>"></div>
                            <div class="col-md-1 d-flex align-items-end"><div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="ev_support" id="ev_e" <?= $spot['ev_support'] ? 'checked' : '' ?>><label class="form-check-label" for="ev_e">EV</label></div></div>
                            <div class="col-md-2"><label class="form-label">Max height (m)</label><input type="number" name="max_vehicle_height_m" class="form-control" step="0.01" value="<?= htmlspecialchars((string)($spot['max_vehicle_height_m'] ?? '')) ?>"></div>
                            <div class="col-md-2"><label class="form-label">Max width (m)</label><input type="number" name="max_vehicle_width_m" class="form-control" step="0.01" value="<?= htmlspecialchars((string)($spot['max_vehicle_width_m'] ?? '')) ?>"></div>
                            <div class="col-md-2"><label class="form-label">Difficulty 1–5</label><input type="number" name="difficulty_rating" class="form-control" min="1" max="5" value="<?= (int)($spot['difficulty_rating'] ?? 3) ?>"></div>
                            <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($spot['description'] ?? '') ?></textarea></div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-warning"><i class="bi bi-save"></i> Update Spot</button>
                                <a href="<?= page_url('owner-spots') ?>" class="btn btn-secondary ms-2">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>