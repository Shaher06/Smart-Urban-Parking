<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-dark text-white text-center"><h5><i class="bi bi-receipt"></i> Payment Receipt</h5></div>
                <div class="card-body">
                    <?php if (!$payment): ?>
                        <div class="alert alert-danger">Receipt not found.</div>
                    <?php else: ?>
                        <table class="table table-borderless">
                            <tr><td><strong>Payment #</strong></td><td><?= $payment['id'] ?></td></tr>
                            <tr><td><strong>Amount</strong></td><td>$<?= number_format($payment['amount'], 2) ?></td></tr>
                            <tr><td><strong>Method</strong></td><td><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $payment['method']))) ?></td></tr>
                            <tr><td><strong>Status</strong></td><td><?= status_badge($payment['status']) ?></td></tr>
                            <tr><td><strong>Transaction Ref</strong></td><td class="font-monospace"><?= htmlspecialchars($payment['transaction_ref'] ?? 'N/A') ?></td></tr>
                            <tr><td><strong>Date</strong></td><td><?= htmlspecialchars($payment['created_at']) ?></td></tr>
                        </table>
                        <div class="text-center mt-3">
                            <span class="badge bg-success fs-6"><i class="bi bi-check-circle"></i> Payment Successful</span>
                        </div>
                    <?php endif; ?>
                    <div class="text-center mt-3">
                        <a href="<?= page_url('payment-history') ?>" class="btn btn-secondary">Back to History</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>