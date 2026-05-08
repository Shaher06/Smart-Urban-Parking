<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <?= render_flash() ?>
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white"><h5><i class="bi bi-credit-card"></i> Make Payment</h5></div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3"><label class="form-label">Amount ($)</label><input type="number" name="amount" class="form-control" step="0.01" min="0.01" required></div>
                        <div class="mb-3"><label class="form-label">Reservation ID (optional)</label><input type="number" name="reservation_id" class="form-control"></div>
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select name="method" class="form-select">
                                <option value="credit_card">Credit Card</option>
                                <option value="debit_card">Debit Card</option>
                                <option value="wallet">Wallet</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100"><i class="bi bi-check-circle"></i> Pay Now</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>