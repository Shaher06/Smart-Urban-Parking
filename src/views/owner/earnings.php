<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-cash-stack"></i> My Earnings</h3>
            <div class="alert alert-success">
                <strong>Total Gross Earnings:</strong> $<?= number_format($total, 2) ?>
                | <strong>Commission (<?= COMMISSION_RATE * 100 ?>%):</strong> $<?= number_format($total * COMMISSION_RATE, 2) ?>
                | <strong>Net Earnings:</strong> $<?= number_format($total * (1 - COMMISSION_RATE), 2) ?>
            </div>
            <?php if (empty($earnings)): ?>
                <div class="alert alert-info">No earnings data yet.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark"><tr><th>Spot</th><th>Bookings</th><th>Gross ($)</th><th>Commission ($)</th><th>Net ($)</th></tr></thead>
                        <tbody>
                            <?php foreach ($earnings as $e): ?>
                                <tr>
                                    <td><?= htmlspecialchars($e['spot_name']) ?></td>
                                    <td><?= $e['bookings'] ?></td>
                                    <td>$<?= number_format($e['gross'], 2) ?></td>
                                    <td class="text-danger">-$<?= number_format($e['commission'], 2) ?></td>
                                    <td class="text-success fw-bold">$<?= number_format($e['net'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            <a href="<?= page_url('owner-payouts') ?>" class="btn btn-primary mt-3"><i class="bi bi-wallet2"></i> Request Payout</a>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>