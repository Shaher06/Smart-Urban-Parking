<?php

require_once BASE_PATH . '/models/User.php';

class SpaceOwner extends User
{
    public function getOwnerStats(int $ownerId): array
    {
        $db = $this->db;

        $spots = $db->prepare("SELECT COUNT(*) FROM parking_spots WHERE owner_id = ?");
        $spots->execute([$ownerId]);
        $totalSpots = (int)$spots->fetchColumn();

        $revenue = $db->prepare(
            "SELECT COALESCE(SUM(p.amount),0) FROM payments p
             JOIN reservations r ON r.id = p.reservation_id
             JOIN parking_spots s ON s.id = r.spot_id
             WHERE s.owner_id = ? AND p.status = 'completed'"
        );
        $revenue->execute([$ownerId]);
        $totalRevenue = (float)$revenue->fetchColumn();

        $pending = $db->prepare(
            "SELECT COUNT(*) FROM reservations r
             JOIN parking_spots s ON s.id = r.spot_id
             WHERE s.owner_id = ? AND r.status = 'confirmed'"
        );
        $pending->execute([$ownerId]);
        $pendingBookings = (int)$pending->fetchColumn();

        return compact('totalSpots', 'totalRevenue', 'pendingBookings');
    }

    public function getTotalEarnings(int $ownerId): float
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(p.amount),0) FROM payments p
             JOIN reservations r ON r.id = p.reservation_id
             JOIN parking_spots s ON s.id = r.spot_id
             WHERE s.owner_id = ? AND p.status = 'completed'"
        );
        $stmt->execute([$ownerId]);
        return (float)$stmt->fetchColumn();
    }
}