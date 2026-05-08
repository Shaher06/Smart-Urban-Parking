<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-receipt"></i> Manage Fines</h3>

            <!-- ── Fine Stats Bar ───────────────────────────────────────────── -->
            <div class="row g-2 mb-3">
                <div class="col"><div class="card text-center p-2 bg-light"><strong><?= $stats['total'] ?></strong><br><small>Total</small></div></div>
                <div class="col"><div class="card text-center p-2 border-danger"><strong class="text-danger"><?= $stats['unpaid'] ?></strong><br><small>Unpaid</small></div></div>
                <div class="col"><div class="card text-center p-2 border-success"><strong class="text-success"><?= $stats['paid'] ?></strong><br><small>Paid</small></div></div>
                <div class="col"><div class="card text-center p-2 border-secondary"><strong class="text-secondary"><?= $stats['waived'] ?></strong><br><small>Waived</small></div></div>
                <div class="col"><div class="card text-center p-2 border-success"><strong class="text-success">$<?= number_format($stats['revenue'], 2) ?></strong><br><small>Collected</small></div></div>
            </div>

            <!-- ── Issue Fine Form ─────────────────────────────────────────── -->
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <i class="bi bi-plus-circle"></i> Issue New Fine
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= page_url('issue-fine') ?>">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">Driver <span class="text-danger">*</span></label>
                                <select name="user_id" class="form-select" required>
                                    <option value="">-- Select Driver --</option>
                                    <?php foreach ($users as $u): ?>
                                        <option value="<?= $u['id'] ?>">
                                            <?= htmlspecialchars($u['name']) ?>
                                            (<?= htmlspecialchars($u['email']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Amount ($) <span class="text-danger">*</span></label>
                                <input type="number" name="amount" class="form-control"
                                       step="0.01" min="1" max="9999" required
                                       placeholder="e.g. 50.00">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Reason <span class="text-danger">*</span></label>
                                <input type="text" name="reason" class="form-control" required
                                       placeholder="e.g. Overstay violation">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Reservation ID (optional)</label>
                                <input type="number" name="reservation_id" class="form-control" min="1">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="bi bi-send"></i> Issue
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ── Fines Table ─────────────────────────────────────────────── -->
            <?php if (empty($fines)): ?>
                <div class="alert alert-info">No fines recorded yet.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th><th>Driver</th><th>Amount</th>
                                <th>Reason</th><th>Issued By</th>
                                <th>Date</th><th>Status</th><th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($fines as $f): ?>
                                <tr>
                                    <td><?= $f['id'] ?></td>
                                    <td><?= htmlspecialchars($f['driver_name']) ?></td>
                                    <td class="fw-bold text-danger">
                                        $<?= number_format($f['amount'], 2) ?>
                                    </td>
                                    <td><?= htmlspecialchars($f['reason']) ?></td>
                                    <td><?= htmlspecialchars($f['officer_name'] ?? 'System') ?></td>
                                    <td><?= htmlspecialchars($f['issued_at']) ?></td>
                                    <td><?= status_badge($f['status']) ?></td>
                                    <td>
                                        <?php if ($f['status'] === 'unpaid'): ?>
                                            <a href="<?= page_url('waive-fine', ['id' => $f['id']]) ?>"
                                               class="btn btn-sm btn-outline-secondary"
                                               onclick="return confirm('Waive this fine?')">
                                                Waive
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">—</span>
                                        <?php endif; ?>
                                    </td>
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