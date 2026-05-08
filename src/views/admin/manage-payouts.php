<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>

<?php
    // Ensure CSRF token exists for admin approval forms
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-wallet2"></i> Manage Owner Payouts</h3>
            <?php if (empty($payouts)): ?>
                <div class="alert alert-info">No payout requests yet.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th><th>Owner</th><th>Gross</th>
                                <th>Commission</th><th>Net</th>
                                <th>Status</th><th>Requested</th><th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payouts as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars((string)$p['id'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars((string)$p['owner_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>$<?= number_format($p['amount'], 2) ?></td>
                                    <td class="text-danger">-$<?= number_format($p['commission'], 2) ?></td>
                                    <td class="text-success fw-bold">$<?= number_format($p['net_amount'], 2) ?></td>
                                    <td><?= status_badge($p['status']) ?></td>
                                    <td><?= htmlspecialchars((string)$p['requested_at'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <?php if ($p['status'] === 'pending'): ?>
                                            <form method="POST">
                                                <input type="hidden" name="csrf_token"
                                                    value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                                                <input type="hidden" name="payout_id"
                                                    value="<?= htmlspecialchars((string)(int)$p['id'], ENT_QUOTES, 'UTF-8') ?>">
                                                <button type="submit" class="btn btn-success btn-sm"
                                                        onclick="return confirm('Mark this payout as paid?')">
                                                    Mark Paid
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted small">
                                                <?= htmlspecialchars((string)($p['paid_at'] ?? '—'), ENT_QUOTES, 'UTF-8') ?>
                                            </span>
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