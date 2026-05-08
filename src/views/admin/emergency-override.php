<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-lightning text-danger"></i> Emergency Override</h3>
            <div class="alert alert-warning"><i class="bi bi-exclamation-triangle"></i> This overrides a parking spot status for emergency access. Optionally cancels an affected reservation and notifies the driver. All actions are logged in the audit trail.</div>

            <?php if (!empty($reports)): ?>
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-danger text-white"><i class="bi bi-list-ul"></i> Open Emergency Reports (<?= count($reports) ?>)</div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-secondary"><tr><th>#</th><th>Driver</th><th>Type</th><th>Spot</th><th>Description</th><th>Time</th></tr></thead>
                        <tbody>
                        <?php foreach ($reports as $rpt): ?>
                            <tr>
                                <td><?= $rpt['id'] ?></td>
                                <td><?= htmlspecialchars($rpt['driver_name']) ?></td>
                                <td><span class="badge bg-danger"><?= htmlspecialchars(str_replace('_',' ',ucfirst($rpt['report_type']))) ?></span></td>
                                <td><?= htmlspecialchars($rpt['spot_name'] ?? '—') ?></td>
                                <td><?= htmlspecialchars(substr($rpt['description'], 0, 80)) ?>…</td>
                                <td><?= htmlspecialchars($rpt['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-dark text-white"><i class="bi bi-lightning"></i> Apply Override</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Select Spot <span class="text-danger">*</span></label>
                                <select name="spot_id" class="form-select" required>
                                    <?php foreach ($spots as $s): ?>
                                        <option value="<?= $s['id'] ?>">[<?= htmlspecialchars($s['status']) ?>] <?= htmlspecialchars($s['name']) ?> — <?= htmlspecialchars($s['city']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Set Status To</label>
                                <select name="target_status" class="form-select">
                                    <option value="active">Active (Force Open)</option>
                                    <option value="inactive">Inactive (Close)</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Reason <span class="text-danger">*</span></label>
                                <input type="text" name="reason" class="form-control" required placeholder="Emergency reason…">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="cancel_reservation" id="cancelRes" value="1">
                                    <label class="form-check-label" for="cancelRes">Cancel active reservation &amp; notify driver</label>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Apply emergency override?')">
                                <i class="bi bi-lightning"></i> Apply Override
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
