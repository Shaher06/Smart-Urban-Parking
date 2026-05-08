<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-slash-circle"></i> Blacklist & Suspensions</h3>
            <ul class="nav nav-tabs mb-3"><li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#blk">Blacklisted (<?= count($blacklisted) ?>)</button></li><li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#sus">Suspended (<?= count($suspended) ?>)</button></li></ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="blk">
                    <?php if (empty($blacklisted)): ?><div class="alert alert-success">No blacklisted users.</div><?php else: ?>
                    <table class="table table-striped"><thead class="table-dark"><tr><th>Name</th><th>Email</th><th>Role</th><th>Action</th></tr></thead><tbody>
                        <?php foreach ($blacklisted as $u): ?>
                            <tr>
                                <td><?= htmlspecialchars($u['name']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><?= htmlspecialchars($u['role']) ?></td>
                                <td>
                                    <form method="POST" action="<?= page_url('update-user-status') ?>" class="d-inline">
                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                        <input type="hidden" name="status" value="active">
                                        <button type="submit" class="btn btn-sm btn-success">Unban</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody></table><?php endif; ?>
                </div>
                <div class="tab-pane fade" id="sus">
                    <?php if (empty($suspended)): ?><div class="alert alert-success">No suspended users.</div><?php else: ?>
                    <table class="table table-striped"><thead class="table-dark"><tr><th>Name</th><th>Email</th><th>Role</th><th>Action</th></tr></thead><tbody>
                        <?php foreach ($suspended as $u): ?>
                            <tr>
                                <td><?= htmlspecialchars($u['name']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><?= htmlspecialchars($u['role']) ?></td>
                                <td>
                                    <form method="POST" action="<?= page_url('update-user-status') ?>" class="d-inline">
                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                        <input type="hidden" name="status" value="active">
                                        <button type="submit" class="btn btn-sm btn-success">Unsuspend</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody></table><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>