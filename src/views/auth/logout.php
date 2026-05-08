<?php
// This view is not rendered directly — logout is handled by AuthController::logout()
// which destroys the session and redirects to login.
header('Location: ' . BASE_URL . '/index.php?page=login');
exit;