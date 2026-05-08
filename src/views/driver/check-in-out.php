<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-qr-code-scan"></i> QR Check In / Out</h3>
            <?php
            $active = array_filter($reservations, fn($r) => in_array($r['status'], ['confirmed','active']));
            ?>
            <?php if (empty($active)): ?>
                <div class="alert alert-info">No active or confirmed reservations to check in/out.</div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($active as $r): ?>
                        <?php $isOvertime = strtotime($r['end_time']) < time() && $r['status'] === 'active'; ?>
                        <div class="col-md-4">
                            <div class="card shadow-sm <?= $isOvertime ? 'border-danger' : '' ?>">
                                <div class="card-body text-center">
                                    <h5><?= htmlspecialchars($r['spot_name']) ?></h5>
                                    <p class="text-muted"><?= htmlspecialchars($r['start_time']) ?> → <?= htmlspecialchars($r['end_time']) ?></p>
                                    <p><?= status_badge($r['status']) ?></p>
                                    <?php if ($isOvertime): ?>
                                        <?php $overMins = (int)((time() - strtotime($r['end_time'])) / 60); ?>
                                        <div class="alert alert-danger py-1 px-2 mb-2">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                            <strong>Overstay: <?= $overMins ?> min</strong><br>
                                            <small>Checkout now to minimise your penalty (<?= OVERSTAY_RATE ?>× rate applies).</small>
                                        </div>
                                    <?php endif; ?>
                                    <div class="bg-light p-3 rounded mb-3">
                                        <i class="bi bi-qr-code" style="font-size:48px"></i>
                                        <p class="small mt-2 font-monospace"><?= htmlspecialchars($r['qr_code'] ?? 'N/A') ?></p>
                                    </div>
                                    <form method="POST">
                                        <input type="hidden" name="reservation_id" value="<?= $r['id'] ?>">
                                        <?php if ($r['status'] === 'confirmed'): ?>
                                            <button name="action" value="checkin" class="btn btn-success w-100"><i class="bi bi-box-arrow-in-right"></i> Check In</button>
                                        <?php else: ?>
                                            <button name="action" value="checkout" class="btn btn-danger w-100"
                                                    <?= $isOvertime ? 'onclick="return confirm(\'You have an overstay penalty. Checkout anyway?\')"' : '' ?>>
                                                <i class="bi bi-box-arrow-right"></i> Check Out
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>