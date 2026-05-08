<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-send"></i> Officer Dispatch</h3>
            <p class="text-muted">Assign enforcement officers to incidents. The officer and driver are both notified instantly.</p>

            <?php if (!empty($overstays)): ?>
            <div class="card mb-4 border-danger shadow-sm">
                <div class="card-header bg-danger text-white">
                    <i class="bi bi-exclamation-circle"></i> Active Overstays (<?= count($overstays) ?>) — Dispatch recommended
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-secondary">
                            <tr><th>Res#</th><th>Driver</th><th>Spot</th><th>End Time</th><th>Overstay</th><th>Quick Dispatch</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($overstays as $o): ?>
                            <?php $mins = (int)((time() - strtotime($o['end_time'])) / 60); ?>
                            <tr class="<?= $mins > 30 ? 'table-danger' : 'table-warning' ?>">
                                <td>#<?= $o['id'] ?></td>
                                <td><?= htmlspecialchars($o['driver_name']) ?></td>
                                <td><?= htmlspecialchars($o['spot_name']) ?><br><small><?= htmlspecialchars($o['address']) ?></small></td>
                                <td><?= htmlspecialchars($o['end_time']) ?></td>
                                <td><strong><?= $mins ?> min</strong> <?= $mins > 30 ? '<span class="badge bg-danger">Serious</span>' : '<span class="badge bg-warning text-dark">Minor</span>' ?></td>
                                <td>
                                    <form method="POST" class="d-flex gap-1 align-items-center">
                                        <input type="hidden" name="driver_id" value="<?= $o['user_id'] ?>">
                                        <input type="hidden" name="incident_type" value="overstay">
                                        <input type="hidden" name="spot_id" value="<?= $o['spot_id'] ?>">
                                        <input type="hidden" name="note" value="Overstay of <?= $mins ?> minutes on reservation #<?= $o['id'] ?>">
                                        <select name="officer_id" class="form-select form-select-sm" style="width:auto" required>
                                            <option value="">-- Officer --</option>
                                            <?php foreach ($officers as $off): ?>
                                                <option value="<?= $off['id'] ?>"><?= htmlspecialchars($off['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-broadcast"></i> Dispatch</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <i class="bi bi-broadcast"></i> Manual Dispatch
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Officer <span class="text-danger">*</span></label>
                                <select name="officer_id" class="form-select" required>
                                    <option value="">-- Select Officer --</option>
                                    <?php foreach ($officers as $o): ?>
                                        <option value="<?= $o['id'] ?>"><?= htmlspecialchars($o['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Target Driver <span class="text-danger">*</span></label>
                                <select name="driver_id" class="form-select" required>
                                    <option value="">-- Select Driver --</option>
                                    <?php foreach ($drivers as $d): ?>
                                        <option value="<?= $d['id'] ?>">
                                            <?= htmlspecialchars($d['name']) ?> (<?= htmlspecialchars($d['email']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Incident Type</label>
                                <select name="incident_type" class="form-select">
                                    <option value="violation">Violation</option>
                                    <option value="overstay">Overstay</option>
                                    <option value="no_payment">No Payment</option>
                                    <option value="obstruction">Obstruction</option>
                                    <option value="emergency">Emergency</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Spot (optional)</label>
                                <select name="spot_id" class="form-select">
                                    <option value="">-- Any --</option>
                                    <?php foreach ($spots as $sp): ?>
                                        <option value="<?= $sp['id'] ?>"><?= htmlspecialchars($sp['name']) ?> (<?= htmlspecialchars($sp['city']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Note</label>
                                <input type="text" name="note" class="form-control" placeholder="Additional instructions">
                            </div>
                        </div>
                        <div class="col-md-4 mt-2">
                            <label class="form-label">Location Override (leave blank to auto-fill from spot)</label>
                            <input type="text" name="location" class="form-control" placeholder="e.g. Spot #3, Block A">
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-secondary">
                                <i class="bi bi-broadcast"></i> Dispatch &amp; Notify Officer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                The selected officer receives an in-system notification immediately. The driver is also notified.
                All dispatch actions are recorded in the <a href="<?= page_url('audit-logs') ?>">Audit Log</a>.
            </div>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
