<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <?= render_flash() ?>
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h5><i class="bi bi-flag"></i> Appeal Fine #<?= $fine['id'] ?></h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-light border">
                        <strong>Fine Amount:</strong> $<?= number_format($fine['amount'], 2) ?><br>
                        <strong>Reason:</strong> <?= htmlspecialchars($fine['reason']) ?>
                    </div>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Appeal Reason <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="4" required placeholder="Explain why you believe this fine should be waived..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Upload Evidence (optional)</label>
                            <input type="file" name="evidence" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                            <div class="form-text">Accepted: JPG, PNG, PDF. Max 5MB.</div>
                        </div>
                        <button type="submit" class="btn btn-warning"><i class="bi bi-send"></i> Submit Appeal</button>
                        <a href="<?= page_url('fines') ?>" class="btn btn-secondary ms-2">Cancel</a>
                    </form>
                </div>
            </div>
        </div>