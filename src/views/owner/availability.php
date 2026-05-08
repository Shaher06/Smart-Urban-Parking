<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-calendar3"></i> Spot Availability</h3>
            <p class="text-muted">Update the status and available slots for each of your parking spots.</p>

            <?php if (empty($spots)): ?>
                <div class="alert alert-info">No spots yet. <a href="<?= page_url('add-spot') ?>">Add your first spot!</a></div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr><th>#</th><th>Name</th><th>City</th><th>Available / Total</th><th>Current Status</th><th>Update</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($spots as $s): ?>
                            <tr>
                                <td><?= $s['id'] ?></td>
                                <td><strong><?= htmlspecialchars($s['name']) ?></strong></td>
                                <td><?= htmlspecialchars($s['city']) ?></td>
                                <td><?= $s['available_slots'] ?> / <?= $s['total_slots'] ?></td>
                                <td><?= status_badge($s['status']) ?></td>
                                <td>
                                    <form method="POST" class="d-flex gap-2 align-items-center flex-wrap">
                                        <input type="hidden" name="spot_id" value="<?= $s['id'] ?>">
                                        <select name="status" class="form-select form-select-sm" style="width:auto">
                                            <?php foreach (['active','inactive','maintenance','owner-use'] as $st): ?>
                                                <option value="<?= $st ?>" <?= $s['status'] === $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <input type="number" name="available_slots" class="form-control form-control-sm" style="width:80px"
                                               value="<?= $s['available_slots'] ?>" min="0" max="<?= $s['total_slots'] ?>">
                                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check-lg"></i> Save</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="alert alert-info mt-3">
                    <i class="bi bi-info-circle"></i>
                    <strong>Status guide:</strong>
                    <strong>Active</strong> — bookable by drivers.
                    <strong>Inactive</strong> — hidden from search.
                    <strong>Maintenance</strong> — temporarily unavailable.
                    <strong>Owner-use</strong> — reserved for personal use.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
