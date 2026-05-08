<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white"><h5><i class="bi bi-trash"></i> Delete Account</h5></div>
                <div class="card-body text-center">
                    <?= render_flash() ?>
                    <p class="text-danger fw-bold">This action is irreversible. All your data will be deleted.</p>
                    <form method="POST">
                        <button type="submit" class="btn btn-danger me-2">Yes, Delete My Account</button>
                        <a href="<?= page_url('profile') ?>" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>