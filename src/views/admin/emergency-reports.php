<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-exclamation-octagon text-danger"></i> Emergency Reports</h3>
            <p class="text-muted">All emergency reports submitted by drivers. Update their status as you handle each case.</p>
            <?php if (empty($reports)): ?>
                <div class="alert alert-info">No emergency reports found.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr><th>#</th><th>Driver</th><th>Type</th><th>Spot</th><th>Description</th><th>Status</th><th>Reported</th><th>Update</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($reports as $r): ?>
                            <tr class="<?= $r['status'] === 'open' ? 'table-danger' : ($r['status'] === 'in_progress' ? 'table-warning' : '') ?>">
                                <td><?= $r['id'] ?></td>
                                <td><?= htmlspecialchars($r['driver_name']) ?><br><small class="text-muted"><?= htmlspecialchars($r['driver_email']) ?></small></td>
                                <td><span class="badge bg-danger"><?= htmlspecialchars(str_replace('_',' ',ucfirst($r['report_type']))) ?></span></td>
                                <td><?= htmlspecialchars($r['spot_name'] ?? '—') ?></td>
                                <td style="max-width:260px"><small><?= nl2br(htmlspecialchars(substr($r['description'],0,150))) ?><?= strlen($r['description']) > 150 ? '…' : '' ?></small>
                                    <?php if ($r['admin_note']): ?><br><em class="text-muted small">Admin note: <?= htmlspecialchars($r['admin_note']) ?></em><?php endif; ?>
                                </td>
                                <td><?= status_badge($r['status']) ?></td>
                                <td><?= htmlspecialchars($r['created_at']) ?></td>
                                <td>
                                    <form method="POST" class="d-flex flex-column gap-1">
                                        <input type="hidden" name="report_id" value="<?= $r['id'] ?>">
                                        <select name="status" class="form-select form-select-sm">
                                            <?php foreach (['open','in_progress','resolved'] as $st): ?>
                                                <option value="<?= $st ?>" <?= $r['status'] === $st ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$st)) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <input type="text" name="admin_note" class="form-control form-control-sm" placeholder="Admin note…" value="<?= htmlspecialchars($r['admin_note'] ?? '') ?>">
                                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                    </form>
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
