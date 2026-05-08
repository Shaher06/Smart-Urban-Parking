<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-flag"></i> Review Appeals</h3>
            <?php if (empty($appeals)): ?>
                <div class="alert alert-success">No pending appeals.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark"><tr><th>#</th><th>Driver</th><th>Fine Reason</th><th>Fine $</th><th>Appeal Reason</th><th>Evidence</th><th>Status</th><th>Review</th></tr></thead>
                        <tbody>
                            <?php foreach ($appeals as $a): ?>
                                <tr>
                                    <td><?= $a['id'] ?></td>
                                    <td><?= htmlspecialchars($a['driver_name']) ?></td>
                                    <td><?= htmlspecialchars($a['fine_reason']) ?></td>
                                    <td>$<?= number_format($a['fine_amount'], 2) ?></td>
                                    <td><?= htmlspecialchars(substr($a['reason'], 0, 60)) ?>...</td>
                                    <td>
                                        <?php if ($a['evidence_file']): ?>
                                            <a href="<?= upload_url($a['evidence_file']) ?>" target="_blank" class="btn btn-sm btn-outline-info">View</a>
                                        <?php else: ?>
                                            <span class="text-muted">None</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= status_badge($a['status']) ?></td>
                                    <td>
                                        <?php if ($a['status'] === 'pending'): ?>
                                            <form method="POST" action="<?= page_url('review-appeal') ?>" class="d-flex gap-1">
                                                <input type="hidden" name="appeal_id" value="<?= $a['id'] ?>">
                                                <input type="text" name="note" class="form-control form-control-sm" placeholder="Note">
                                                <button name="decision" value="approved" class="btn btn-success btn-sm">Approve</button>
                                                <button name="decision" value="rejected" class="btn btn-danger btn-sm">Reject</button>
                                            </form>
                                        <?php else: ?>
                                            <small class="text-muted"><?= htmlspecialchars($a['admin_note'] ?? '') ?></small>
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