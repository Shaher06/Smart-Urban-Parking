<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container mt-4">
    <?= render_flash() ?>
    <h3><i class="bi bi-map"></i> Navigate to Parking Spot</h3>
    <?php if (!$reservation): ?>
        <div class="alert alert-danger">Reservation not found.</div>
    <?php else: ?>
        <div class="card shadow-sm">
            <div class="card-body">
                <h5><?= htmlspecialchars($reservation['spot_name']) ?></h5>
                <p><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($reservation['address']) ?>, <?= htmlspecialchars($reservation['city']) ?></p>
                <p><strong>Start:</strong> <?= htmlspecialchars($reservation['start_time']) ?> | <strong>End:</strong> <?= htmlspecialchars($reservation['end_time']) ?></p>
                <?php
                require_once BASE_PATH . '/services/NavigationService.php';
                $nav = new NavigationService();
                $link = $nav->getNavigationLink(
                    $reservation['latitude'] ?? 0,
                    $reservation['longitude'] ?? 0,
                    $reservation['address']
                );
                ?>
                <a href="<?= htmlspecialchars($link) ?>" target="_blank" class="btn btn-primary">
                    <i class="bi bi-map-fill"></i> Open in Google Maps
                </a>
                <a href="<?= page_url('reservations') ?>" class="btn btn-secondary ms-2">Back</a>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>