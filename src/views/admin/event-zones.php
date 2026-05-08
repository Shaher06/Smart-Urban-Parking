<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-lock"></i> Event Zone Locking</h3>
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">Lock Event Zone</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row g-2">
                            <div class="col-md-3"><label class="form-label">Zone Name</label><input type="text" name="name" class="form-control" required placeholder="e.g. Stadium Zone A"></div>
                            <div class="col-md-3"><label class="form-label">Description</label><input type="text" name="description" class="form-control"></div>
                            <div class="col-md-2"><label class="form-label">Affected Spot IDs</label><input type="text" name="affected_spot_ids" class="form-control" placeholder="1,2,3"></div>
                            <div class="col-md-2"><label class="form-label">Start</label><input type="datetime-local" name="start_time" class="form-control" required></div>
                            <div class="col-md-2"><label class="form-label">End</label><input type="datetime-local" name="end_time" class="form-control" required></div>
                        </div>
                        <button type="submit" class="btn btn-dark mt-3">Lock Zone</button>
                    </form>
                </div>
            </div>
            <?php if (empty($zones)): ?>
                <div class="alert alert-info">No active event zones.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark"><tr><th>Name</th><th>Description</th><th>Spots</th><th>Start</th><th>End</th><th>Locked By</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php foreach ($zones as $z): ?>
                                <tr>
                                    <td><?= htmlspecialchars($z['name']) ?></td>
                                    <td><?= htmlspecialchars($z['description'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($z['affected_spot_ids'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($z['start_time']) ?></td>
                                    <td><?= htmlspecialchars($z['end_time']) ?></td>
                                    <td><?= htmlspecialchars($z['locked_by_name'] ?? 'N/A') ?></td>
                                    <td><?= status_badge($z['status']) ?></td>
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