<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-activity"></i> Real-Time Occupancy</h3>
            <p class="text-muted">Live parking spot occupancy based on current reservations.</p>

            <div class="row g-3">
                <?php foreach ($live_data as $spot):
                    $pct   = (float)$spot['occupancy_pct'];
                    $color = $pct >= 80 ? 'danger' : ($pct >= 50 ? 'warning' : ($pct >= 25 ? 'info' : 'success'));
                    $label = $pct >= 80 ? 'Very Busy' : ($pct >= 50 ? 'Busy' : ($pct >= 25 ? 'Moderate' : 'Free'));
                ?>
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0"><?= htmlspecialchars($spot['name']) ?></h6>
                                    <span class="badge bg-<?= $color ?>"><?= $label ?></span>
                                </div>
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($spot['city']) ?>
                                </p>
                                <div class="progress mb-2" style="height:18px">
                                    <div class="progress-bar bg-<?= $color ?> progress-bar-striped"
                                         style="width:<?= $pct ?>%">
                                        <?= $pct ?>%
                                    </div>
                                </div>
                                <small class="text-muted">
                                    <?= $spot['available_slots'] ?> free of <?= $spot['total_slots'] ?> total slots
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (empty($live_data)): ?>
                <div class="alert alert-info mt-3">No active parking spots found.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>