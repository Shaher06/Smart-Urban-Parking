<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-chat"></i> Messages</h3>

            <div class="card mb-4">
                <div class="card-header bg-primary text-white">Send Message to Owner</div>
                <div class="card-body">
                    <form method="POST" action="<?= page_url('send-message') ?>">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">To (Owner)</label>
                                <select name="receiver_id" class="form-select" required>
                                    <?php foreach ($owners as $o): ?><option value="<?= $o['id'] ?>"><?= htmlspecialchars($o['name']) ?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Subject</label>
                                <input type="text" name="subject" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Message</label>
                                <input type="text" name="body" class="form-control" required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Send</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <ul class="nav nav-tabs mb-3" id="msgTab">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#inbox">Inbox (<?= count($inbox) ?>)</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#sent">Sent (<?= count($sent) ?>)</button></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="inbox">
                    <?php if (empty($inbox)): ?><div class="alert alert-info">No messages.</div><?php else: ?>
                    <table class="table table-striped">
                        <thead class="table-dark"><tr><th>From</th><th>Subject</th><th>Message</th><th>Read</th><th>Date</th></tr></thead>
                        <tbody>
                            <?php foreach ($inbox as $m): ?>
                                <tr class="<?= !$m['is_read'] ? 'fw-bold' : '' ?>">
                                    <td><?= htmlspecialchars($m['sender_name']) ?></td>
                                    <td><?= htmlspecialchars($m['subject'] ?? '(no subject)') ?></td>
                                    <td><?= htmlspecialchars(substr($m['body'], 0, 60)) ?>...</td>
                                    <td><?= $m['is_read'] ? '✓' : '<span class="badge bg-danger">New</span>' ?></td>
                                    <td><?= htmlspecialchars($m['created_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
                <div class="tab-pane fade" id="sent">
                    <?php if (empty($sent)): ?><div class="alert alert-info">No sent messages.</div><?php else: ?>
                    <table class="table table-striped">
                        <thead class="table-dark"><tr><th>To</th><th>Subject</th><th>Message</th><th>Date</th></tr></thead>
                        <tbody>
                            <?php foreach ($sent as $m): ?>
                                <tr>
                                    <td><?= htmlspecialchars($m['receiver_name']) ?></td>
                                    <td><?= htmlspecialchars($m['subject'] ?? '(no subject)') ?></td>
                                    <td><?= htmlspecialchars(substr($m['body'], 0, 60)) ?>...</td>
                                    <td><?= htmlspecialchars($m['created_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>