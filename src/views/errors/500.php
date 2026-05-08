<?php if (!defined('BASE_PATH')) { define('BASE_PATH', dirname(__DIR__, 2)); } ?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>500 Error</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"></head>
<body>
<div class="container text-center mt-5">
    <div class="card shadow-sm border-danger">
        <div class="card-body p-5">
            <h1 class="display-1 text-danger">500</h1>
            <h3>Internal Server Error</h3>
            <p class="text-muted">Something went wrong on our end. Please try again later.</p>
            <a href="/Smart_Parking/src/public/index.php?page=home" class="btn btn-danger">Go Home</a>
        </div>
    </div>
</div>
</body></html>