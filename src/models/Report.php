<?php

require_once BASE_PATH . '/core/Model.php';

/**
 * Report Model — provides analytical queries across multiple tables.
 * Does NOT map to a single table (queries payments, reservations, parking_spots).
 */
class Report extends Model
{
    // No single $table — this model aggregates across tables.
    protected string $table = '';

    public function getRevenueByMonth(): array
    {
        $stmt = $this->db->query(
            "SELECT DATE_FORMAT(created_at,'%Y-%m') as month,
             SUM(amount) as total
             FROM payments
             WHERE status='completed'
             GROUP BY month
             ORDER BY month DESC
             LIMIT 12"
        );
        return $stmt->fetchAll();
    }

    public function getRevenueBySpot(): array
    {
        $stmt = $this->db->query(
            "SELECT ps.name as spot_name, ps.city,
             COUNT(r.id) as total_reservations,
             COALESCE(SUM(p.amount),0) as revenue
             FROM parking_spots ps
             LEFT JOIN reservations r ON r.spot_id = ps.id
             LEFT JOIN payments p ON p.reservation_id = r.id AND p.status='completed'
             GROUP BY ps.id
             ORDER BY revenue DESC"
        );
        return $stmt->fetchAll();
    }

    public function getOwnerEarnings(int $ownerId): array
    {
        $stmt = $this->db->prepare(
            "SELECT ps.name as spot_name,
             COUNT(r.id) as bookings,
             COALESCE(SUM(p.amount),0) as gross,
             COALESCE(SUM(p.amount),0) * :commission as commission,
             COALESCE(SUM(p.amount),0) * (1 - :commission2) as net
             FROM parking_spots ps
             LEFT JOIN reservations r ON r.spot_id = ps.id AND r.status = 'completed'
             LEFT JOIN payments p ON p.reservation_id = r.id AND p.status='completed'
             WHERE ps.owner_id = :owner_id
             GROUP BY ps.id"
        );
        $stmt->execute([
            ':commission'  => COMMISSION_RATE,
            ':commission2' => COMMISSION_RATE,
            ':owner_id'    => $ownerId,
        ]);
        return $stmt->fetchAll();
    }
}