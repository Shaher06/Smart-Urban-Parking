<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container mt-4">
    <?= render_flash() ?>
    <div class="row">
        <div class="col-md-4 text-center">
            <?php if (!empty($user['profile_image'])): ?>
                <img src="<?= upload_url($user['profile_image']) ?>" class="rounded-circle mb-3" width="120" height="120" style="object-fit:cover">
            <?php else: ?>
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:120px;height:120px;font-size:48px">
                    <i class="bi bi-person"></i>
                </div>
            <?php endif; ?>
            <h4><?= htmlspecialchars($user['name']) ?></h4>
            <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'owner' ? 'success' : 'primary') ?>">
                <?= ucfirst(htmlspecialchars($user['role'])) ?>
            </span>
            <p class="mt-2 text-muted small">Member since <?= date('M Y', strtotime($user['created_at'])) ?></p>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h5>Profile Details</h5></div>
                <div class="card-body">
                    <table class="table">
                        <tr><th>Name</th><td><?= htmlspecialchars($user['name']) ?></td></tr>
                        <tr><th>Email</th><td><?= htmlspecialchars($user['email']) ?></td></tr>
                        <tr><th>Phone</th><td><?= htmlspecialchars($user['phone'] ?? 'N/A') ?></td></tr>
                        <tr><th>Status</th><td><?= status_badge($user['status']) ?></td></tr>
                        <tr><th>Language</th><td><?= htmlspecialchars(strtoupper($user['language'])) ?></td></tr>
                    </table>
                    <a href="<?= page_url('edit-profile') ?>" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit Profile</a>
                    <a href="<?= page_url('delete-profile') ?>" class="btn btn-danger ms-2"><i class="bi bi-trash"></i> Delete Account</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>