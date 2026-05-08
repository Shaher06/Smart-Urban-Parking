<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-receipt-cutoff"></i> Tax Details</h3>
            <div class="card mb-4">
                <div class="card-header">Generate Tax Report</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Tax Year</label>
                                <input type="number" name="year" class="form-control" value="<?= date('Y') ?>" min="2020" max="<?= date('Y') ?>">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">Generate</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php if (!empty($taxData)): ?>
                <div class="alert alert-info">
                    <strong>Year:</strong> <?= $taxData['year'] ?> |
                    <strong>Total Revenue:</strong> $<?= number_format($taxData['revenue'], 2) ?> |
                    <strong>Tax (<?= TAX_RATE * 100 ?>%):</strong> $<?= number_format($taxData['tax'], 2) ?> |
                    <strong>VAT (<?= VAT_RATE * 100 ?>%):</strong> $<?= number_format($taxData['vat'], 2) ?> |
                    <strong>Generated:</strong> <?= date('Y-m-d H:i') ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($history)): ?>
                <h5>Tax History</h5>
                <table class="table table-striped">
                    <thead class="table-dark"><tr><th>Year</th><th>Revenue ($)</th><th>Tax ($)</th><th>VAT ($)</th><th>Generated</th></tr></thead>
                    <tbody>
                        <?php foreach ($history as $h): ?>
                            <tr>
                                <td><?= $h['tax_year'] ?></td>
                                <td>$<?= number_format($h['total_revenue'], 2) ?></td>
                                <td>$<?= number_format($h['tax_amount'], 2) ?></td>
                                <td>$<?= number_format($h['vat_amount'], 2) ?></td>
                                <td><?= htmlspecialchars($h['generated_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>