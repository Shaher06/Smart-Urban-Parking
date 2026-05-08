<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-star"></i> My Reviews</h3>

            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">Add a Review</div>
                <div class="card-body">
                    <form method="POST" action="<?= page_url('add-review') ?>">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label">Parking Spot</label>
                                <select name="spot_id" class="form-select" required>
                                    <?php foreach ($spots as $s): ?>
                                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Rating</label>
                                <select name="rating" class="form-select">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <option value="<?= $i ?>"><?= $i ?> Star<?= $i > 1 ? 's' : '' ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Comment</label>
                                <input type="text" name="comment" class="form-control" placeholder="Your review...">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-warning w-100">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (empty($reviews)): ?>
                <div class="alert alert-info">No reviews yet.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark"><tr><th>Spot</th><th>Rating</th><th>Comment</th><th>Date</th></tr></thead>
                        <tbody>
                            <?php foreach ($reviews as $r): ?>
                                <tr>
                                    <td><?= htmlspecialchars($r['spot_name']) ?></td>
                                    <td><?= str_repeat('⭐', (int)$r['rating']) ?></td>
                                    <td><?= htmlspecialchars($r['comment'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($r['created_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>