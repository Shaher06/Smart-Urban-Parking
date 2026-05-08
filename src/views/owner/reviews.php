<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-star"></i> Reviews for My Spots</h3>
            <?php if (empty($reviews)): ?>
                <div class="alert alert-info">No reviews yet.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark"><tr><th>Spot</th><th>Driver</th><th>Rating</th><th>Comment</th><th>Date</th></tr></thead>
                        <tbody>
                            <?php foreach ($reviews as $r): ?>
                                <tr>
                                    <td><?= htmlspecialchars($r['spot_name']) ?></td>
                                    <td><?= htmlspecialchars($r['reviewer_name']) ?></td>
                                    <td><?= str_repeat('⭐', (int)$r['rating']) ?></td>
                                    <td><?= htmlspecialchars($r['comment'] ?? 'No comment') ?></td>
                                    <td><?= htmlspecialchars($r['created_at']) ?></td>
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