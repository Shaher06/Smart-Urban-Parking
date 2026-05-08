<?php

require_once BASE_PATH . '/models/User.php';

class EnforcementOfficer extends User
{
    public function getDispatchedCases(int $officerId): array
    {
        $stmt = $this->db->prepare(
            "SELECT al.*, u.name as target_user
             FROM audit_logs al
             LEFT JOIN users u ON u.id = al.user_id
             WHERE al.description LIKE '%dispatch%' AND al.action = 'officer_dispatch'
             ORDER BY al.created_at DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function issueFine(int $userId, float $amount, string $reason, int $officerId): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO fines (user_id, issued_by, amount, reason, status)
             VALUES (?, ?, ?, ?, 'unpaid')"
        );
        $stmt->execute([$userId, $officerId, $amount, $reason]);
        return (int)$this->db->lastInsertId();
    }
}