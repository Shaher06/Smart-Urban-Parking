<?php

require_once BASE_PATH . '/core/Model.php';

class Appeal extends Model
{
    protected string $table = 'appeals';

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO appeals (fine_id, user_id, reason, evidence_file, status)
             VALUES (:fine_id,:user_id,:reason,:evidence_file,:status)"
        );
        $stmt->execute([
            ':fine_id'       => $data['fine_id'],
            ':user_id'       => $data['user_id'],
            ':reason'        => $data['reason'],
            ':evidence_file' => $data['evidence_file'] ?? null,
            ':status'        => 'pending',
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT a.*, f.reason as fine_reason, f.amount as fine_amount
             FROM appeals a
             JOIN fines f ON f.id = a.fine_id
             WHERE a.user_id = ?
             ORDER BY a.created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT a.*, u.name as driver_name, f.reason as fine_reason, f.amount as fine_amount
             FROM appeals a
             JOIN users u ON u.id = a.user_id
             JOIN fines f ON f.id = a.fine_id
             ORDER BY a.created_at DESC"
        );
        return $stmt->fetchAll();
    }

    public function review(int $id, string $status, string $note, int $adminId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE appeals SET status=?, admin_note=?, reviewed_by=? WHERE id=?"
        );
        return $stmt->execute([$status, $note, $adminId, $id]);
    }

    public function hasPendingForFine(int $fineId): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM appeals WHERE fine_id=? AND status='pending'"
        );
        $stmt->execute([$fineId]);
        return (int)$stmt->fetchColumn() > 0;
    }
}