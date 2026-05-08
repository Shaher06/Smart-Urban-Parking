<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-clock-history"></i> Reservation History</h3>
            <?php $completed = array_filter($reservations, fn($r) => in_array($r['status'], ['completed','cancelled'])); ?>
            <?php if (empty($completed)): ?>
                <div class="alert alert-info">No completed or cancelled reservations yet.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark"><tr><th>#</th><th>Spot</th><th>Start</th><th>End</th><th>Price</th><th>Refund</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php foreach ($completed as $r): ?>
                                <tr>
                                    <td><?= $r['id'] ?></td>
                                    <td><?= htmlspecialchars($r['spot_name']) ?></td>
                                    <td><?= htmlspecialchars($r['start_time']) ?></td>
                                    <td><?= htmlspecialchars($r['end_time']) ?></td>
                                    <td>$<?= number_format($r['total_price'], 2) ?></td>
                                    <td>$<?= number_format($r['refund_amount'], 2) ?></td>
                                    <td><?= status_badge($r['status']) ?></td>
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