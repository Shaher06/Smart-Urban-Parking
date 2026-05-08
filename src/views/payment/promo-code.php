<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <?= render_flash() ?>
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark"><h5><i class="bi bi-tag"></i> Check Promo Code</h5></div>
                <div class="card-body">
                    <form method="POST">
                        <div class="input-group mb-3">
                            <input type="text" name="code" class="form-control" placeholder="Enter promo code" required>
                            <button type="submit" class="btn btn-warning">Check</button>
                        </div>
                    </form>
                    <?php if ($result !== null): ?>
                        <?php if ($result['found']): ?>
                            <div class="alert alert-success">
                                <strong>Valid!</strong> Promo code "<?= htmlspecialchars($result['promo']['code']) ?>" gives
                                <strong><?= $result['promo']['discount_percent'] ?>% discount</strong>.<br>
                                Valid until: <?= htmlspecialchars($result['promo']['valid_until']) ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger">Invalid or expired promo code.</div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>