<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-search"></i> Browse Parking Spots</h3>

            <form method="GET" class="card p-3 mb-4">
                <input type="hidden" name="page" value="browse-spots">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($filters['city'] ?? '') ?>" placeholder="e.g. New York">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select">
                            <option value="">All</option>
                            <option value="public" <?= ($filters['type'] ?? '') === 'public' ? 'selected' : '' ?>>Public</option>
                            <option value="private" <?= ($filters['type'] ?? '') === 'private' ? 'selected' : '' ?>>Private</option>
                            <option value="reserved" <?= ($filters['type'] ?? '') === 'reserved' ? 'selected' : '' ?>>Reserved</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Max Price/hr</label>
                        <input type="number" name="max_price" class="form-control" value="<?= htmlspecialchars($filters['max_price'] ?? '') ?>" min="0" step="0.5">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Min height (m)</label>
                        <input type="number" name="min_height" class="form-control" step="0.01" min="0" value="<?= htmlspecialchars((string)($filters['min_height'] ?? '')) ?>" placeholder="Any">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Min width (m)</label>
                        <input type="number" name="min_width" class="form-control" step="0.01" min="0" value="<?= htmlspecialchars((string)($filters['min_width'] ?? '')) ?>" placeholder="Any">
                    </div>
                </div>
                <div class="row g-2 align-items-end mt-1">
                    <div class="col-md-2">
                        <label class="form-label">Max difficulty (1–5)</label>
                        <input type="number" name="max_difficulty" class="form-control" min="1" max="5" value="<?= htmlspecialchars((string)($filters['max_difficulty'] ?? '')) ?>" placeholder="Any">
                    </div>
                    <div class="col-md-2">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="ev" value="1" id="ev" <?= !empty($filters['ev']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="ev">EV Support</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="available_only" value="1" id="avail" <?= !empty($filters['available_only']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="avail">Available only</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Filter</button>
                    </div>
                </div>
            </form>

            <?php if (empty($spots)): ?>
                <div class="alert alert-info">No parking spots found. Try adjusting your filters.</div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($spots as $spot): ?>
                        <div class="col-md-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($spot['name']) ?></h5>
                                    <p class="text-muted small"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($spot['address']) ?>, <?= htmlspecialchars($spot['city']) ?></p>
                                    <p><strong>$<?= number_format($spot['price_per_hour'], 2) ?>/hr</strong></p>
                                    <p>
                                        <?= status_badge($spot['type']) ?>
                                        <?php if ($spot['ev_support']): ?><span class="badge bg-success"><i class="bi bi-lightning"></i> EV</span><?php endif; ?>
                                        <?php if ($spot['available_slots'] > 0): ?><span class="badge bg-info"><?= $spot['available_slots'] ?> slots</span><?php else: ?><span class="badge bg-danger">Full</span><?php endif; ?>
                                    </p>
                                    <p class="small text-warning"><i class="bi bi-star-fill"></i> <?= number_format($spot['avg_rating'], 1) ?>
                                        <?php if (!empty($spot['difficulty_rating'])): ?> | <i class="bi bi-cone-striped"></i> Difficulty <?= (int)$spot['difficulty_rating'] ?>/5<?php endif; ?>
                                    </p>
                                    <?php if (!empty($spot['max_vehicle_height_m']) || !empty($spot['max_vehicle_width_m'])): ?>
                                        <p class="small text-muted mb-0">Max vehicle:
                                            <?php if (!empty($spot['max_vehicle_height_m'])): ?>H <?= htmlspecialchars((string)$spot['max_vehicle_height_m']) ?>m<?php endif; ?>
                                            <?php if (!empty($spot['max_vehicle_width_m'])): ?> W <?= htmlspecialchars((string)$spot['max_vehicle_width_m']) ?>m<?php endif; ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer d-flex gap-2">
                                    <?php if ($spot['available_slots'] > 0): ?>
                                        <a href="<?= page_url('book-spot', ['id' => $spot['id']]) ?>" class="btn btn-primary btn-sm flex-fill"><i class="bi bi-calendar-plus"></i> Book</a>
                                    <?php else: ?>
                                        <a href="<?= page_url('waitlist') ?>" class="btn btn-warning btn-sm flex-fill"><i class="bi bi-hourglass"></i> Waitlist</a>
                                    <?php endif; ?>
                                    <a href="<?= page_url('toggle-favorite', ['spot_id' => $spot['id']]) ?>" class="btn btn-outline-danger btn-sm"><i class="bi bi-heart"></i></a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>