<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<?php $default_vehicle_id = (int)($default_vehicle_id ?? 0); ?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <?= render_flash() ?>
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5><i class="bi bi-calendar-plus"></i> Book: <?= htmlspecialchars($spot['name']) ?></h5>
                </div>
                <div class="card-body">
                    <p><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($spot['address']) ?>, <?= htmlspecialchars($spot['city']) ?></p>
                    <p><strong>Price:</strong> $<?= number_format($spot['price_per_hour'], 2) ?>/hr | <strong>Available:</strong> <?= $spot['available_slots'] ?> slots</p>
                    <?php if ($spot['ev_support']): ?><span class="badge bg-success mb-3"><i class="bi bi-lightning"></i> EV Charging Available</span><?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Start Time</label>
                            <input type="datetime-local" name="start_time" class="form-control" required min="<?= date('Y-m-d\TH:i') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">End Time</label>
                            <input type="datetime-local" name="end_time" class="form-control" required>
                        </div>
                        <?php if (!empty($vehicles)): ?>
                        <div class="mb-3">
                            <label class="form-label">Select Vehicle</label>
                            <select name="vehicle_id" class="form-select">
                                <option value="">-- No Vehicle --</option>
                                <?php foreach ($vehicles as $v): ?>
                                    <?php $sel = !empty($default_vehicle_id) && (int)$v['id'] === (int)$default_vehicle_id ? 'selected' : ''; ?>
                                    <option value="<?= (int)$v['id'] ?>" <?= $sel ?>><?= htmlspecialchars($v['plate_number']) ?> (<?= htmlspecialchars($v['make'] . ' ' . $v['model']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label">Promo Code (optional)</label>
                            <input type="text" name="promo_code" class="form-control" placeholder="e.g. WELCOME10">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-select">
                                <option value="credit_card">Credit Card</option>
                                <option value="debit_card">Debit Card</option>
                                <option value="wallet">Wallet</option>
                            </select>
                        </div>
                        <div class="alert alert-info small">
                            <i class="bi bi-info-circle"></i> Buffer time of <?= BUFFER_MINUTES ?> minutes applies between bookings. Grace period: <?= GRACE_MINUTES ?> minutes.
                        </div>
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check-circle"></i> Confirm Booking</button>
                    </form>
                </div>
            </div>
            <a href="<?= page_url('browse-spots') ?>" class="btn btn-link mt-2">&larr; Back to Browse</a>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>