<?php

require_once BASE_PATH . '/core/Model.php';

class FileUpload extends Model
{
    protected string $table = 'file_uploads';

    public function record(int $userId, string $type, string $name, string $path, int $relatedId = null): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO file_uploads (user_id, file_type, file_name, file_path, related_id)
             VALUES (?,?,?,?,?)"
        );
        $stmt->execute([$userId, $type, $name, $path, $relatedId]);
        return (int)$this->db->lastInsertId();
    }

    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM file_uploads WHERE user_id=? ORDER BY uploaded_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getByType(string $type): array
    {
        $stmt = $this->db->prepare(
            "SELECT fu.*, u.name as user_name
             FROM file_uploads fu
             JOIN users u ON u.id = fu.user_id
             WHERE fu.file_type=?
             ORDER BY fu.uploaded_at DESC"
        );
        $stmt->execute([$type]);
        return $stmt->fetchAll();
    }
}