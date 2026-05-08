<?php

require_once BASE_PATH . '/core/Model.php';

/**
 * Favorites Model — maps to the FavoriteSpot entity in the class diagram.
 * Represents a driver's saved/favourite parking spots.
 */
class Favorites extends Model
{
    protected string $table = 'favorites';

    /**
     * Add a spot to a user's favourites.
     */
    public function add(int $userId, int $spotId): int
    {
        $stmt = $this->db->prepare(
            "INSERT IGNORE INTO favorites (user_id, spot_id) VALUES (?, ?)"
        );
        $stmt->execute([$userId, $spotId]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Remove a spot from a user's favourites.
     */
    public function remove(int $userId, int $spotId): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM favorites WHERE user_id = ? AND spot_id = ?"
        );
        return $stmt->execute([$userId, $spotId]);
    }

    /**
     * Check if a spot is already in the user's favourites.
     */
    public function isFavorite(int $userId, int $spotId): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM favorites WHERE user_id = ? AND spot_id = ?"
        );
        $stmt->execute([$userId, $spotId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Get all favourite spots for a user, joined with spot details.
     */
    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT f.*, ps.name AS spot_name, ps.city, ps.price_per_hour, ps.status,
             ps.available_slots, ps.address
             FROM favorites f
             JOIN parking_spots ps ON ps.id = f.spot_id
             WHERE f.user_id = ?
             ORDER BY f.created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}