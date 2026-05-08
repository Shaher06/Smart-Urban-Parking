<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-patch-check"></i> Owner Verification</h3>
            <div class="table-responsive mb-4">
                <table class="table table-striped">
                    <thead class="table-dark"><tr><th>Owner</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php foreach ($owners as $o): ?>
                            <tr>
                                <td><?= htmlspecialchars($o['name']) ?> (<?= htmlspecialchars($o['email']) ?>)</td>
                                <td><?= status_badge($o['status']) ?></td>
                                <td>
                                    <form method="POST" class="d-flex gap-2">
                                        <input type="hidden" name="owner_id" value="<?= $o['id'] ?>">
                                        <button name="action" value="verify" class="btn btn-success btn-sm">Verify</button>
                                        <button name="action" value="reject" class="btn btn-danger btn-sm" onclick="return confirm('Reject this owner?')">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <h5>Submitted Documents</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark"><tr><th>User</th><th>File</th><th>Uploaded</th></tr></thead>
                    <tbody>
                        <?php foreach ($docs as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars($d['user_name']) ?></td>
                                <td><?= htmlspecialchars($d['file_name']) ?></td>
                                <td><?= htmlspecialchars($d['uploaded_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>