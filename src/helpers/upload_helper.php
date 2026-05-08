<?php

function handle_upload(array $file, string $subDir, array $allowedTypes = ['image/jpeg','image/png','application/pdf']): string|false
{
    if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $mime = mime_content_type($file['tmp_name']);
    if (!in_array($mime, $allowedTypes, true)) {
        return false;
    }

    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = uniqid('file_', true) . '.' . $ext;
    $dir      = UPLOAD_PATH . '/' . $subDir;

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $dest = $dir . '/' . $fileName;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return false;
    }

    return $subDir . '/' . $fileName;
}

function delete_upload(string $relativePath): void
{
    $full = UPLOAD_PATH . '/' . ltrim($relativePath, '/');
    if (file_exists($full)) {
        unlink($full);
    }
}