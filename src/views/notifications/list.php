<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container mt-4">
    <?= render_flash() ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3><i class="bi bi-bell"></i> Notifications</h3>
        <a href="<?= page_url('mark-notification-read') ?>" class="btn btn-sm btn-outline-secondary">Mark all as read</a>
    </div>
    <?php if (empty($notifications)): ?>
        <div class="alert alert-info">No notifications yet.</div>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($notifications as $n): ?>
                <div class="list-group-item <?= !$n['is_read'] ? 'list-group-item-light fw-bold border-start border-primary border-4' : '' ?>">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-<?= match($n['type']) { 'booking'=>'primary','payment'=>'success','fine'=>'danger','appeal'=>'warning','message'=>'info',default=>'secondary' } ?>"><?= ucfirst($n['type']) ?></span>
                            <strong class="ms-2"><?= htmlspecialchars($n['title']) ?></strong>
                            <p class="mb-0 mt-1 text-secondary"><?= htmlspecialchars($n['message']) ?></p>
                        </div>
                        <div class="text-end text-muted small">
                            <?= htmlspecialchars($n['created_at']) ?><br>
                            <?php if (!$n['is_read']): ?>
                                <a href="<?= page_url('mark-notification-read', ['id' => $n['id']]) ?>" class="btn btn-sm btn-link p-0">Mark read</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>