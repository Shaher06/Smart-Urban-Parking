<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container mt-4">
    <?= render_flash() ?>
    <h3><i class="bi bi-file-earmark-bar-graph"></i> Generate Report</h3>
    <form method="GET" class="mb-3">
        <input type="hidden" name="page" value="generate-report">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Report Type</label>
                <select name="type" class="form-select">
                    <option value="revenue" <?= $type === 'revenue' ? 'selected' : '' ?>>Monthly Revenue</option>
                    <option value="spots" <?= $type === 'spots' ? 'selected' : '' ?>>By Spot</option>
                </select>
            </div>
            <div class="col-md-2"><button type="submit" class="btn btn-primary">Generate</button></div>
        </div>
    </form>
    <?php if (!empty($data)): ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-dark"><tr><?php foreach (array_keys($data[0]) as $col): ?><th><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $col))) ?></th><?php endforeach; ?></tr></thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                        <tr><?php foreach ($row as $cell): ?><td><?= htmlspecialchars($cell) ?></td><?php endforeach; ?></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>