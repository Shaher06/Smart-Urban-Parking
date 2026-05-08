<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container mt-4">
    <?= render_flash() ?>
    <h3><i class="bi bi-receipt"></i> Payment History</h3>
    <p class="text-muted">
        <span class="badge bg-warning text-dark">Pending</span>
        <span class="badge bg-info text-dark">Escrow (locked)</span>
        <span class="badge bg-success">Completed</span>
        <span class="badge bg-danger">Failed</span>
        <span class="badge bg-secondary">Refunded</span>
    </p>
    <?php if (empty($payments)): ?>
        <div class="alert alert-info">No payments yet.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr><th>#</th><th>Spot / Fine</th><th>Amount</th><th>Method</th><th>Status</th><th>Escrow</th><th>Ref</th><th>Date</th><th>Receipt</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><?= htmlspecialchars($p['spot_name'] ?? ($p['fine_id'] ? 'Fine #'.$p['fine_id'] : 'N/A')) ?></td>
                            <td>$<?= number_format($p['amount'], 2) ?></td>
                            <td><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $p['method']))) ?></td>
                            <td>
                                <?php
                                $badgeMap = [
                                    'pending'   => 'bg-warning text-dark',
                                    'escrow'    => 'bg-info text-dark',
                                    'completed' => 'bg-success',
                                    'failed'    => 'bg-danger',
                                    'refunded'  => 'bg-secondary',
                                ];
                                $cls = $badgeMap[$p['status']] ?? 'bg-light text-dark';
                                ?>
                                <span class="badge <?= $cls ?>"><?= ucfirst($p['status']) ?></span>
                            </td>
                            <td>
                                <?php if ($p['escrow_locked']): ?>
                                    <span class="badge bg-warning text-dark"><i class="bi bi-lock"></i> Locked</span>
                                <?php endif; ?>
                                <?php if ($p['escrow_released']): ?>
                                    <span class="badge bg-success"><i class="bi bi-unlock"></i> Released</span>
                                <?php endif; ?>
                            </td>
                            <td class="font-monospace small"><?= htmlspecialchars($p['transaction_ref'] ?? '') ?></td>
                            <td><?= htmlspecialchars($p['created_at']) ?></td>
                            <td><a href="<?= page_url('receipt', ['id' => $p['id']]) ?>" class="btn btn-sm btn-outline-secondary">View</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
