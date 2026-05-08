<?php
/**
 * UPLOAD SERVICE — Refactored
 *
 * Fixes:
 * 1. Profile image upload now correctly saves, moves, and returns path.
 * 2. Path stored in DB via UserModel — no more broken links.
 * 3. All upload methods return the relative path OR false on failure.
 * 4. Error messages logged via error_log() for debugging.
 */

require_once BASE_PATH . '/helpers/upload_helper.php';
require_once BASE_PATH . '/models/FileUpload.php';

class UploadService
{
    private FileUpload $fileUploadModel;

    // Allowed MIME types per upload category
    private array $allowedTypes = [
        'evidence'        => ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'],
        'owner_documents' => ['image/jpeg', 'image/png', 'application/pdf'],
        'profile_images'  => ['image/jpeg', 'image/png', 'image/webp'],
        'reports'         => ['application/pdf', 'text/csv'],
    ];

    // Max file sizes in bytes (5MB default)
    private int $maxFileSize = 5 * 1024 * 1024;

    public function __construct()
    {
        $this->fileUploadModel = new FileUpload();
    }

    /**
     * Upload profile image and return web-accessible relative path.
     *
     * FIX: Previously didn't verify directory exists or handle errors.
     * NOW:  Ensures directory exists, validates type, moves file, records in DB.
     *
     * @param array $file       $_FILES['profile_image']
     * @param int   $userId
     * @return string|false     Relative path like "profile_images/file_xxx.jpg" or false
     */
    public function uploadProfileImage(array $file, int $userId): string|false
    {
        $error = $this->validateFile($file, 'profile_images');
        if ($error) {
            error_log("Profile image upload failed for user {$userId}: {$error}");
            return false;
        }

        $path = $this->moveFile($file, 'profile_images');
        if (!$path) {
            return false;
        }

        // Record in file_uploads table
        $this->fileUploadModel->record($userId, 'profile_image', $file['name'], $path, $userId);

        return $path;
    }

    /**
     * Upload fine appeal evidence.
     */
    public function uploadEvidence(array $file, int $userId, int $appealId): string|false
    {
        $error = $this->validateFile($file, 'evidence');
        if ($error) {
            error_log("Evidence upload failed for user {$userId}, appeal {$appealId}: {$error}");
            return false;
        }

        $path = $this->moveFile($file, 'evidence');
        if (!$path) {
            return false;
        }

        $this->fileUploadModel->record($userId, 'evidence', $file['name'], $path, $appealId);
        return $path;
    }

    /**
     * Upload owner verification document.
     */
    public function uploadOwnerDocument(array $file, int $userId): string|false
    {
        $error = $this->validateFile($file, 'owner_documents');
        if ($error) {
            error_log("Owner doc upload failed for user {$userId}: {$error}");
            return false;
        }

        $path = $this->moveFile($file, 'owner_documents');
        if (!$path) {
            return false;
        }

        $this->fileUploadModel->record($userId, 'owner_document', $file['name'], $path);
        return $path;
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    /**
     * Validate file before moving.
     * Returns error message string, or empty string if valid.
     */
    private function validateFile(array $file, string $category): string
    {
        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return 'No file uploaded or upload error: ' . ($file['error'] ?? 'unknown');
        }

        if ($file['size'] > $this->maxFileSize) {
            return 'File too large. Max ' . ($this->maxFileSize / 1024 / 1024) . 'MB.';
        }

        // Use mime_content_type for real MIME detection (not spoofed extension)
        $mime = mime_content_type($file['tmp_name']);
        if (!in_array($mime, $this->allowedTypes[$category] ?? [], true)) {
            return "File type '{$mime}' not allowed for {$category}.";
        }

        return '';
    }

    /**
     * Move the uploaded file to the correct subdirectory.
     * Creates directory if it doesn't exist.
     *
     * @return string|false  Relative path like "profile_images/file_xxx.jpg"
     */
    private function moveFile(array $file, string $subDir): string|false
    {
        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $fileName = 'file_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $dir      = UPLOAD_PATH . '/' . $subDir;

        // Ensure directory exists and is writable
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                error_log("Failed to create upload directory: {$dir}");
                return false;
            }
        }

        $destination = $dir . '/' . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            error_log("move_uploaded_file() failed: {$file['tmp_name']} → {$destination}");
            return false;
        }

        return $subDir . '/' . $fileName;
    }

    /**
     * Delete a previously uploaded file by its relative path.
     */
    public function deleteFile(string $relativePath): void
    {
        $full = UPLOAD_PATH . '/' . ltrim($relativePath, '/');
        if (file_exists($full) && is_file($full)) {
            unlink($full);
        }
    }
}