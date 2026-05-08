<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-shield-check"></i> Owner Verification</h3>
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">Upload Verification Document</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Ownership Document (PDF/Image)</label>
                            <input type="file" name="document" class="form-control" required accept=".jpg,.jpeg,.png,.pdf">
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-cloud-upload"></i> Submit Document</button>
                    </form>
                </div>
            </div>
            <?php if (!empty($docs)): ?>
                <h5>Submitted Documents</h5>
                <table class="table table-striped">
                    <thead class="table-dark"><tr><th>File</th><th>Type</th><th>Uploaded</th></tr></thead>
                    <tbody>
                        <?php foreach ($docs as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars($d['file_name']) ?></td>
                                <td><?= htmlspecialchars($d['file_type']) ?></td>
                                <td><?= htmlspecialchars($d['uploaded_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>