<?php

require_once BASE_PATH . '/core/Model.php';

class Review extends Model
{
    protected string $table = 'reviews';

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO reviews (user_id, spot_id, reservation_id, rating, comment)
             VALUES (:user_id,:spot_id,:reservation_id,:rating,:comment)"
        );
        $stmt->execute([
            ':user_id'        => $data['user_id'],
            ':spot_id'        => $data['spot_id'],
            ':reservation_id' => $data['reservation_id'] ?? null,
            ':rating'         => $data['rating'],
            ':comment'        => $data['comment'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function getBySpot(int $spotId): array
    {
        $stmt = $this->db->prepare(
            "SELECT rv.*, u.name as reviewer_name
             FROM reviews rv
             JOIN users u ON u.id = rv.user_id
             WHERE rv.spot_id = ?
             ORDER BY rv.created_at DESC"
        );
        $stmt->execute([$spotId]);
        return $stmt->fetchAll();
    }

    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT rv.*, ps.name as spot_name
             FROM reviews rv
             JOIN parking_spots ps ON ps.id = rv.spot_id
             WHERE rv.user_id = ?
             ORDER BY rv.created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getForOwnerSpots(int $ownerId): array
    {
        $stmt = $this->db->prepare(
            "SELECT rv.*, u.name as reviewer_name, ps.name as spot_name
             FROM reviews rv
             JOIN users u ON u.id = rv.user_id
             JOIN parking_spots ps ON ps.id = rv.spot_id
             WHERE ps.owner_id = ?
             ORDER BY rv.created_at DESC"
        );
        $stmt->execute([$ownerId]);
        return $stmt->fetchAll();
    }

    public function hasReviewed(int $userId, int $spotId): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM reviews WHERE user_id=? AND spot_id=?"
        );
        $stmt->execute([$userId, $spotId]);
        return (int)$stmt->fetchColumn() > 0;
    }
}