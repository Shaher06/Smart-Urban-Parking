<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-journal-text"></i> Audit Logs</h3>
            <p class="text-muted">Last 200 system actions</p>
            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover">
                    <thead class="table-dark"><tr><th>#</th><th>User</th><th>Action</th><th>Description</th><th>IP</th><th>Time</th></tr></thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?= $log['id'] ?></td>
                                <td><?= htmlspecialchars($log['user_name'] ?? 'System') ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($log['action']) ?></span></td>
                                <td><?= htmlspecialchars($log['description'] ?? '') ?></td>
                                <td class="font-monospace small"><?= htmlspecialchars($log['ip_address'] ?? '') ?></td>
                                <td><?= htmlspecialchars($log['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>