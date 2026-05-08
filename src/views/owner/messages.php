<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-chat"></i> Messages</h3>
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">Send Message to Driver</div>
                <div class="card-body">
                    <form method="POST" action="<?= page_url('send-message') ?>">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">To (Driver)</label>
                                <select name="receiver_id" class="form-select" required>
                                    <?php foreach ($drivers as $d): ?><option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3"><label class="form-label">Subject</label><input type="text" name="subject" class="form-control"></div>
                            <div class="col-md-4"><label class="form-label">Message</label><input type="text" name="body" class="form-control" required></div>
                            <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn btn-primary w-100">Send</button></div>
                        </div>
                    </form>
                </div>
            </div>
            <ul class="nav nav-tabs mb-3"><li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#inbox">Inbox</button></li><li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#sent">Sent</button></li></ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="inbox">
                    <?php if (empty($inbox)): ?><div class="alert alert-info">Empty inbox.</div><?php else: ?>
                    <table class="table table-striped"><thead class="table-dark"><tr><th>From</th><th>Subject</th><th>Message</th><th>Date</th></tr></thead><tbody>
                        <?php foreach ($inbox as $m): ?><tr><td><?= htmlspecialchars($m['sender_name']) ?></td><td><?= htmlspecialchars($m['subject'] ?? '') ?></td><td><?= htmlspecialchars(substr($m['body'], 0, 60)) ?>...</td><td><?= htmlspecialchars($m['created_at']) ?></td></tr><?php endforeach; ?>
                    </tbody></table><?php endif; ?>
                </div>
                <div class="tab-pane fade" id="sent">
                    <?php if (empty($sent)): ?><div class="alert alert-info">No sent messages.</div><?php else: ?>
                    <table class="table table-striped"><thead class="table-dark"><tr><th>To</th><th>Subject</th><th>Message</th><th>Date</th></tr></thead><tbody>
                        <?php foreach ($sent as $m): ?><tr><td><?= htmlspecialchars($m['receiver_name']) ?></td><td><?= htmlspecialchars($m['subject'] ?? '') ?></td><td><?= htmlspecialchars(substr($m['body'], 0, 60)) ?>...</td><td><?= htmlspecialchars($m['created_at']) ?></td></tr><?php endforeach; ?>
                    </tbody></table><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>