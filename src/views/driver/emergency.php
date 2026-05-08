<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-exclamation-octagon text-danger"></i> Report an Emergency</h3>
            <p class="text-muted">Use this for urgent safety or security issues at a parking location. Your report is logged and sent to administrators and enforcement officers immediately.</p>
            <div class="card shadow-sm" style="max-width:680px">
                <div class="card-header bg-danger text-white"><i class="bi bi-broadcast"></i> Emergency Report Form</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Emergency Type <span class="text-danger">*</span></label>
                            <select name="report_type" class="form-select" required>
                                <option value="accident">Accident</option>
                                <option value="illegal_parking">Illegal Parking</option>
                                <option value="safety_issue">Safety Issue</option>
                                <option value="blocked_spot">Blocked Spot</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="5" required
                                      placeholder="Describe the location, spot name, and what you observed…"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Related Parking Spot (optional)</label>
                            <select name="spot_id" class="form-select">
                                <option value="">-- None --</option>
                                <?php if (!empty($activeSpots)): ?>
                                    <?php foreach ($activeSpots as $sp): ?>
                                        <option value="<?= $sp['id'] ?>"><?= htmlspecialchars($sp['name']) ?> (<?= htmlspecialchars($sp['city']) ?>)</option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Related Reservation (optional)</label>
                            <select name="reservation_id" class="form-select">
                                <option value="">-- None --</option>
                                <?php if (!empty($activeReservations)): ?>
                                    <?php foreach ($activeReservations as $res): ?>
                                        <option value="<?= $res['id'] ?>">#<?= $res['id'] ?> — <?= htmlspecialchars($res['spot_name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger"><i class="bi bi-send"></i> Submit Report</button>
                            <a href="<?= page_url('driver-dashboard') ?>" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
