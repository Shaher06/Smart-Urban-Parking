<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-exclamation-triangle"></i> My Fines</h3>

            <?php if (!empty($pay_fine)): ?>
                <div class="card mb-4 border-danger">
                    <div class="card-header bg-danger text-white">Pay Fine #<?= $pay_fine['id'] ?></div>
                    <div class="card-body">
                        <p><strong>Amount:</strong> $<?= number_format($pay_fine['amount'], 2) ?></p>
                        <p><strong>Reason:</strong> <?= htmlspecialchars($pay_fine['reason']) ?></p>
                        <form method="POST" action="<?= page_url('pay-fine', ['id' => $pay_fine['id']]) ?>">
                            <div class="mb-3">
                                <label class="form-label">Payment Method</label>
                                <select name="method" class="form-select">
                                    <option value="credit_card">Credit Card</option>
                                    <option value="debit_card">Debit Card</option>
                                    <option value="wallet">Wallet</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-danger"><i class="bi bi-credit-card"></i> Pay $<?= number_format($pay_fine['amount'], 2) ?></button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (empty($fines)): ?>
                <div class="alert alert-success"><i class="bi bi-check-circle"></i> No fines! Great driving!</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark"><tr><th>#</th><th>Amount</th><th>Reason</th><th>Issued By</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            <?php foreach ($fines as $f): ?>
                                <tr>
                                    <td><?= $f['id'] ?></td>
                                    <td class="fw-bold text-danger">$<?= number_format($f['amount'], 2) ?></td>
                                    <td><?= htmlspecialchars($f['reason']) ?></td>
                                    <td><?= htmlspecialchars($f['issued_by_name'] ?? 'System') ?></td>
                                    <td><?= htmlspecialchars($f['issued_at']) ?></td>
                                    <td><?= status_badge($f['status']) ?></td>
                                    <td>
                                        <?php if ($f['status'] === 'unpaid'): ?>
                                            <a href="<?= page_url('pay-fine', ['id' => $f['id']]) ?>" class="btn btn-danger btn-sm">Pay</a>
                                            <a href="<?= page_url('appeal-fine', ['id' => $f['id']]) ?>" class="btn btn-warning btn-sm">Appeal</a>
                                        <?php endif; ?>
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