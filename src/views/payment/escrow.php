<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container mt-4">
    <?= render_flash() ?>
    <h3><i class="bi bi-safe"></i> Escrow Payments</h3>
    <p class="text-muted">Funds locked in escrow until your parking session is checked out and completed. Escrow is automatically released on checkout.</p>
    <?php
    $escrow = array_filter($payments ?? [], fn($p) => $p['status'] === 'escrow');
    ?>
    <?php if (empty($escrow)): ?>
        <div class="alert alert-info"><i class="bi bi-info-circle"></i> No funds currently in escrow. Escrow payments appear here when you have an active booking.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr><th>#</th><th>Spot</th><th>Amount</th><th>Method</th><th>Locked</th><th>Transaction Ref</th><th>Date</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($escrow as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><?= htmlspecialchars($p['spot_name'] ?? 'N/A') ?></td>
                            <td><strong>$<?= number_format($p['amount'], 2) ?></strong></td>
                            <td><?= htmlspecialchars(ucfirst(str_replace('_',' ',$p['method']))) ?></td>
                            <td>
                                <?php if ($p['escrow_locked'] && !$p['escrow_released']): ?>
                                    <span class="badge bg-warning text-dark"><i class="bi bi-lock-fill"></i> Locked in escrow</span>
                                <?php else: ?>
                                    <span class="badge bg-success"><i class="bi bi-unlock-fill"></i> Released</span>
                                <?php endif; ?>
                            </td>
                            <td class="font-monospace small"><?= htmlspecialchars($p['transaction_ref'] ?? '') ?></td>
                            <td><?= htmlspecialchars($p['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    <div class="alert alert-secondary mt-3">
        <i class="bi bi-info-circle"></i>
        <strong>How escrow works:</strong> When you book a spot, payment is locked in escrow. On checkout, funds are released to the spot owner. If you cancel, a partial or full refund is issued according to our <strong>cancellation policy</strong>.
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
