<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-plus-circle"></i> Add New Parking Spot</h3>
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Spot Name *</label><input type="text" name="name" class="form-control" required></div>
                            <div class="col-md-6"><label class="form-label">Address *</label><input type="text" name="address" class="form-control" required></div>
                            <div class="col-md-4"><label class="form-label">City *</label><input type="text" name="city" class="form-control" required></div>
                            <div class="col-md-4"><label class="form-label">Type</label>
                                <select name="type" class="form-select"><option value="public">Public</option><option value="private">Private</option><option value="reserved">Reserved</option></select>
                            </div>
                            <div class="col-md-4"><label class="form-label">Price per Hour ($) *</label><input type="number" name="price_per_hour" class="form-control" step="0.01" min="0" required></div>
                            <div class="col-md-3"><label class="form-label">Total Slots</label><input type="number" name="total_slots" class="form-control" min="1" value="1"></div>
                            <div class="col-md-3"><label class="form-label">Latitude</label><input type="number" name="latitude" class="form-control" step="any"></div>
                            <div class="col-md-3"><label class="form-label">Longitude</label><input type="number" name="longitude" class="form-control" step="any"></div>
                            <div class="col-md-3 d-flex align-items-end"><div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="ev_support" id="ev_s"><label class="form-check-label" for="ev_s">EV Charging Support</label></div></div>
                            <div class="col-md-2"><label class="form-label">Max vehicle height (m)</label><input type="number" name="max_vehicle_height_m" class="form-control" step="0.01" min="0" placeholder="e.g. 2.1"></div>
                            <div class="col-md-2"><label class="form-label">Max vehicle width (m)</label><input type="number" name="max_vehicle_width_m" class="form-control" step="0.01" min="0" placeholder="e.g. 2.5"></div>
                            <div class="col-md-2"><label class="form-label">Access difficulty (1–5)</label><input type="number" name="difficulty_rating" class="form-control" min="1" max="5" value="3"></div>
                            <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success"><i class="bi bi-plus-circle"></i> Add Spot</button>
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