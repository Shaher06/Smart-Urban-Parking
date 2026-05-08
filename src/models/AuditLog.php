<?php

require_once BASE_PATH . '/core/Model.php';

class AuditLog extends Model
{
    protected string $table = 'audit_logs';

    public function log(?int $userId = null, string $action = '', string $description = ''): void
    {
        $ip   = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $stmt = $this->db->prepare(
            "INSERT INTO audit_logs (user_id, action, description, ip_address)
             VALUES (?,?,?,?)"
        );
        $stmt->execute([$userId, $action, $description, $ip]);
    }

    public function getAll(int $limit = 100): array
    {
        $stmt = $this->db->prepare(
            "SELECT al.*, u.name as user_name
             FROM audit_logs al
             LEFT JOIN users u ON u.id = al.user_id
             ORDER BY al.created_at DESC
             LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}