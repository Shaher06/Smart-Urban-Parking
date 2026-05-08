<?php

require_once BASE_PATH . '/core/Model.php';

class Reservation extends Model
{
    protected string $table = 'reservations';

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO reservations
             (user_id, spot_id, vehicle_id, start_time, end_time, status, total_price, qr_code, promo_code_id)
             VALUES
             (:user_id,:spot_id,:vehicle_id,:start_time,:end_time,:status,:total_price,:qr_code,:promo_code_id)"
        );
        $stmt->execute([
            ':user_id'       => $data['user_id'],
            ':spot_id'       => $data['spot_id'],
            ':vehicle_id'    => $data['vehicle_id'] ?? null,
            ':start_time'    => $data['start_time'],
            ':end_time'      => $data['end_time'],
            ':status'        => $data['status'] ?? 'confirmed',
            ':total_price'   => $data['total_price'],
            ':qr_code'       => $data['qr_code'] ?? null,
            ':promo_code_id' => $data['promo_code_id'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT r.*, ps.name as spot_name, ps.address, ps.city
             FROM reservations r
             JOIN parking_spots ps ON ps.id = r.spot_id
             WHERE r.user_id = ?
             ORDER BY r.created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getDetailedById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT r.*, ps.name as spot_name, ps.address, ps.city,
             ps.price_per_hour, ps.latitude, ps.longitude,
             u.name as driver_name, u.email as driver_email,
             v.plate_number, v.make, v.model
             FROM reservations r
             JOIN parking_spots ps ON ps.id = r.spot_id
             JOIN users u ON u.id = r.user_id
             LEFT JOIN vehicles v ON v.id = r.vehicle_id
             WHERE r.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * True if another booking overlaps this window, including buffer after each reservation end.
     * Window is [start, end) where end is typically already extended by BUFFER for the new request.
     */
    public function hasConflict(int $spotId, string $start, string $end, int $excludeId = 0, ?int $bufferMinutes = null): bool
    {
        $buf = $bufferMinutes ?? (defined('BUFFER_MINUTES') ? (int) BUFFER_MINUTES : 10);
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM reservations
             WHERE spot_id = ? AND id != ?
             AND status NOT IN ('cancelled','completed')
             AND start_time < ?
             AND DATE_ADD(end_time, INTERVAL ? MINUTE) > ?"
        );
        $stmt->execute([$spotId, $excludeId, $end, $buf, $start]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function countCompletedByUser(int $userId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM reservations WHERE user_id = ? AND status = 'completed'"
        );
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    public function cancel(int $id, float $refund): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE reservations SET status = 'cancelled', refund_amount = ? WHERE id = ?"
        );
        return $stmt->execute([$refund, $id]);
    }

    public function extend(int $id, string $newEndTime, float $extraCost): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE reservations SET end_time = ?, total_price = total_price + ? WHERE id = ?"
        );
        return $stmt->execute([$newEndTime, $extraCost, $id]);
    }

    public function checkIn(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE reservations SET status='active', actual_checkin=NOW() WHERE id=?"
        );
        return $stmt->execute([$id]);
    }

    public function checkOut(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE reservations SET status='completed', actual_checkout=NOW() WHERE id=?"
        );
        return $stmt->execute([$id]);
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE reservations SET status=? WHERE id=?");
        return $stmt->execute([$status, $id]);
    }

    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT r.*, u.name as driver_name, ps.name as spot_name
             FROM reservations r
             JOIN users u ON u.id = r.user_id
             JOIN parking_spots ps ON ps.id = r.spot_id
             ORDER BY r.created_at DESC"
        );
        return $stmt->fetchAll();
    }
}