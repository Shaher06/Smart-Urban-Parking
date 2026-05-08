<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php require_once BASE_PATH . '/views/layouts/sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <?= render_flash() ?>
            <h3><i class="bi bi-people"></i> Manage Users</h3>

            <form method="GET" class="d-flex gap-2 mb-3">
                <input type="hidden" name="page" value="admin-users">
                <input type="text" name="search" class="form-control" placeholder="Search by name or email" value="<?= htmlspecialchars($search ?? '') ?>">
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="<?= page_url('admin-users') ?>" class="btn btn-secondary">Reset</a>
            </form>

            <div class="card mb-4">
                <div class="card-header bg-success text-white">Add New User</div>
                <div class="card-body">
                    <form method="POST" action="<?= page_url('add-user') ?>">
                        <div class="row g-2">
                            <div class="col-md-3"><input type="text" name="name" class="form-control" placeholder="Name" required></div>
                            <div class="col-md-3"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
                            <div class="col-md-2"><input type="password" name="password" class="form-control" placeholder="Password" required></div>
                            <div class="col-md-2"><input type="text" name="phone" class="form-control" placeholder="Phone"></div>
                            <div class="col-md-1">
                                <select name="role" class="form-select">
                                    <option value="driver">Driver</option>
                                    <option value="owner">Owner</option>
                                    <option value="officer">Officer</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <div class="col-md-1"><button type="submit" class="btn btn-success w-100">Add</button></div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark"><tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th>Joined</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?= $u['id'] ?></td>
                                <td><?= htmlspecialchars($u['name']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><?= htmlspecialchars($u['phone'] ?? '-') ?></td>
                                <td><span class="badge bg-primary"><?= htmlspecialchars($u['role']) ?></span></td>
                                <td><?= status_badge($u['status']) ?></td>
                                <td><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                                <td>
                                    <form method="POST" action="<?= page_url('update-user-status') ?>" class="d-inline">
                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                        <select name="status" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                                            <?php foreach (['active','suspended','blacklisted'] as $s): ?>
                                                <option value="<?= $s ?>" <?= $u['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                    <a href="<?= page_url('delete-user', ['id' => $u['id']]) ?>" class="btn btn-danger btn-sm ms-1" onclick="return confirm('Delete this user?')">Del</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>