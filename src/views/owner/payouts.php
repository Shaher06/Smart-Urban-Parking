<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-wallet2"></i> Payouts</h3>

            <?php
                // Show available balance so the owner knows what they can withdraw
                $payoutModel    = new Payout();
                $availableNet   = $payoutModel->getAvailableNet(current_user_id());
                $hasPending     = $payoutModel->ownerHasPending(current_user_id());
            ?>

            <div class="alert alert-info">
                <strong>Available Balance:</strong>
                $<?= number_format($availableNet, 2) ?>
                <?php if ($hasPending): ?>
                    &mdash; <span class="text-warning fw-bold">You have a pending payout request.</span>
                <?php endif; ?>
            </div>

            <form method="POST">
                <?php
                    // CSRF protection
                    if (empty($_SESSION['csrf_token'])) {
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    }
                ?>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="btn btn-success mb-4"
                    <?= ($availableNet <= 0 || $hasPending) ? 'disabled' : '' ?>>
                    <i class="bi bi-send"></i> Request Payout
                </button>
            </form>

            <?php if (empty($payouts)): ?>
                <div class="alert alert-info">No payout requests yet.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Gross ($)</th>
                                <th>Commission ($)</th>
                                <th>Net ($)</th>
                                <th>Status</th>
                                <th>Requested</th>
                                <th>Paid</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payouts as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars((string)$p['id'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>$<?= number_format($p['amount'], 2) ?></td>
                                    <td class="text-danger">-$<?= number_format($p['commission'], 2) ?></td>
                                    <td class="text-success fw-bold">$<?= number_format($p['net_amount'], 2) ?></td>
                                    <td><?= status_badge($p['status']) ?></td>
                                    <td><?= htmlspecialchars((string)$p['requested_at'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= $p['paid_at'] ? htmlspecialchars((string)$p['paid_at'], ENT_QUOTES, 'UTF-8') : '-' ?></td>
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