<?php if (!defined('BASE_PATH')) { define('BASE_PATH', dirname(__DIR__, 2)); } ?>
<?php if (!function_exists('render_flash')) { require_once BASE_PATH . '/helpers/flash_helper.php'; } ?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>404 Not Found</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"></head>
<body>
<div class="container text-center mt-5">
    <div class="card shadow-sm border-warning">
        <div class="card-body p-5">
            <h1 class="display-1 text-warning">404</h1>
            <h3>Page Not Found</h3>
            <p class="text-muted">The page you are looking for does not exist.</p>
            <a href="/Smart_Parking/src/public/index.php?page=home" class="btn btn-primary">Go Home</a>
        </div>
    </div>
</div>
</body></html>