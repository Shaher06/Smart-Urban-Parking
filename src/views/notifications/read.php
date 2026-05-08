<?php
// Redirect to list — individual read is handled via mark-read action
header('Location: ' . BASE_URL . '/index.php?page=notifications');
exit;