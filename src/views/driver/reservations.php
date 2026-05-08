<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-calendar-check"></i> My Reservations</h3>
            <div class="alert alert-info py-2 px-3 mb-3">
                <strong><i class="bi bi-info-circle"></i> Cancellation Refund Policy:</strong>
                &nbsp;Cancel <strong>&gt; 2 hours before</strong> start → 100% refund &nbsp;|&nbsp;
                <strong>1–2 hours before</strong> → 50% refund &nbsp;|&nbsp;
                <strong>&lt; 1 hour before</strong> → no refund.
            </div>
            <?php if (empty($reservations)): ?>
                <div class="alert alert-info">No reservations found. <a href="<?= page_url('browse-spots') ?>">Browse spots</a> to book.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr><th>#</th><th>Spot</th><th>Start</th><th>End</th><th>Price</th><th>Status</th><th>QR</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations as $r): ?>
                                <tr>
                                    <td><?= $r['id'] ?></td>
                                    <td><?= htmlspecialchars($r['spot_name']) ?><br><small class="text-muted"><?= htmlspecialchars($r['city']) ?></small></td>
                                    <td><?= htmlspecialchars($r['start_time']) ?></td>
                                    <td><?= htmlspecialchars($r['end_time']) ?></td>
                                    <td>$<?= number_format($r['total_price'], 2) ?></td>
                                    <td><?= status_badge($r['status']) ?></td>
                                    <td><small class="text-muted font-monospace"><?= htmlspecialchars($r['qr_code'] ?? 'N/A') ?></small></td>
                                    <td class="d-flex flex-wrap gap-1">
                                        <?php if (in_array($r['status'], ['confirmed','pending'])): ?>
                                            <a href="<?= page_url('cancel-reservation', ['id' => $r['id']]) ?>"
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Cancel this reservation?')">Cancel</a>
                                        <?php endif; ?>
                                        <?php if (in_array($r['status'], ['confirmed','pending','active'])): ?>
                                            <button class="btn btn-warning btn-sm"
                                                    onclick="openExtend(<?= $r['id'] ?>)">
                                                <i class="bi bi-clock-history"></i> Extend
                                            </button>
                                        <?php endif; ?>
                                        <a href="<?= page_url('navigate', ['id' => $r['id']]) ?>" class="btn btn-info btn-sm">Navigate</a>
                                    </td>
                                </tr>
                                <?php if (isset($extend_id) && (int)$extend_id === (int)$r['id']): ?>
                                <tr class="table-warning">
                                    <td colspan="8">
                                        <form method="POST" action="<?= page_url('extend-reservation', ['id' => $r['id']]) ?>" class="d-flex align-items-center gap-2">
                                            <strong><i class="bi bi-clock-history"></i> Extend Reservation #<?= $r['id'] ?>:</strong>
                                            <label class="mb-0">Extra hours (1–12):</label>
                                            <input type="number" name="extra_hours" class="form-control form-control-sm" style="width:80px" min="1" max="12" value="1">
                                            <button type="submit" class="btn btn-primary btn-sm">Confirm Extension</button>
                                            <a href="<?= page_url('reservations') ?>" class="btn btn-outline-secondary btn-sm">Cancel</a>
                                        </form>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Extend modal (inline row shown via JS redirect) -->
<script>
function openExtend(id) {
    window.location = '<?= page_url('extend-reservation') ?>&id=' + id;
}
</script>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
