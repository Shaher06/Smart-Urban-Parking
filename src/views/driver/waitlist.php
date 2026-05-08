<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-hourglass"></i> Waitlist</h3>

            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">Join Waitlist</div>
                <div class="card-body">
                    <form method="POST" action="<?= page_url('join-waitlist') ?>">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label">Parking Spot</label>
                                <select name="spot_id" class="form-select" required>
                                    <?php foreach ($spots as $s): ?>
                                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?> - <?= htmlspecialchars($s['city']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Requested Start</label>
                                <input type="datetime-local" name="requested_start" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Requested End</label>
                                <input type="datetime-local" name="requested_end" class="form-control" required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-warning w-100">Join</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (empty($items)): ?>
                <div class="alert alert-info">You are not on any waitlist.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark"><tr><th>Spot</th><th>City</th><th>Start</th><th>End</th><th>Status</th><th>Action</th></tr></thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['spot_name']) ?></td>
                                    <td><?= htmlspecialchars($item['city']) ?></td>
                                    <td><?= htmlspecialchars($item['requested_start']) ?></td>
                                    <td><?= htmlspecialchars($item['requested_end']) ?></td>
                                    <td><?= status_badge($item['status']) ?></td>
                                    <td>
                                        <?php if ($item['status'] === 'waiting'): ?>
                                            <a href="<?= page_url('leave-waitlist', ['id' => $item['id']]) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Leave waitlist?')">Leave</a>
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