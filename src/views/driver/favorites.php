<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-heart"></i> My Favorites</h3>
            <?php if (empty($favorites)): ?>
                <div class="alert alert-info">No favorites yet. <a href="<?= page_url('browse-spots') ?>">Browse spots</a> and add some!</div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($favorites as $f): ?>
                        <div class="col-md-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <h5><?= htmlspecialchars($f['spot_name']) ?></h5>
                                    <p class="text-muted"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($f['city']) ?></p>
                                    <p><strong>$<?= number_format($f['price_per_hour'], 2) ?>/hr</strong></p>
                                    <?= status_badge($f['status']) ?>
                                </div>
                                <div class="card-footer d-flex gap-2">
                                    <a href="<?= page_url('book-spot', ['id' => $f['spot_id']]) ?>" class="btn btn-primary btn-sm flex-fill">Book</a>
                                    <a href="<?= page_url('toggle-favorite', ['spot_id' => $f['spot_id']]) ?>" class="btn btn-outline-danger btn-sm"><i class="bi bi-heart-fill"></i> Remove</a>
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