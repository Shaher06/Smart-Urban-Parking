<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header"><h5><i class="bi bi-translate"></i> Change Language</h5></div>
                <div class="card-body">
                    <?= render_flash() ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Select Language</label>
                            <select name="language" class="form-select">
                                <option value="en">English</option>
                                <option value="ar">Arabic</option>
                                <option value="fr">French</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>