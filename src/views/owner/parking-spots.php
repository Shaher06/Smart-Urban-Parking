<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3><i class="bi bi-geo-alt"></i> My Parking Spots</h3>
                <a href="<?= page_url('add-spot') ?>" class="btn btn-success"><i class="bi bi-plus"></i> Add Spot</a>
            </div>
            <?php if (empty($spots)): ?>
                <div class="alert alert-info">No spots yet. <a href="<?= page_url('add-spot') ?>">Add your first spot!</a></div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark"><tr><th>#</th><th>Name</th><th>Address</th><th>City</th><th>Type</th><th>Price/hr</th><th>Slots</th><th>EV</th><th>Rating</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            <?php foreach ($spots as $s): ?>
                                <tr>
                                    <td><?= $s['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($s['name']) ?></strong></td>
                                    <td><?= htmlspecialchars($s['address']) ?></td>
                                    <td><?= htmlspecialchars($s['city']) ?></td>
                                    <td><?= ucfirst($s['type']) ?></td>
                                    <td>$<?= number_format($s['price_per_hour'], 2) ?></td>
                                    <td><?= $s['available_slots'] ?>/<?= $s['total_slots'] ?></td>
                                    <td><?= $s['ev_support'] ? '<span class="badge bg-success"><i class="bi bi-lightning"></i></span>' : '-' ?></td>
                                    <td>
                                        <?php if ($s['review_count'] > 0): ?>
                                            <span title="<?= $s['review_count'] ?> reviews">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="bi bi-star<?= $i <= round($s['avg_rating']) ? '-fill text-warning' : '' ?>"></i>
                                                <?php endfor; ?>
                                                <small class="text-muted">(<?= $s['review_count'] ?>)</small>
                                            </span>
                                        <?php else: ?>
                                            <small class="text-muted">No reviews</small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= status_badge($s['status']) ?></td>
                                    <td>
                                        <a href="<?= page_url('edit-spot', ['id' => $s['id']]) ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="<?= page_url('delete-spot', ['id' => $s['id']]) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this spot?')">Delete</a>
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