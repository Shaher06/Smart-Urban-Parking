<?php

require_once BASE_PATH . '/core/Model.php';

class Sensor extends Model
{
    protected string $table = 'sensors';

    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT s.*, ps.name as spot_name, ps.city
             FROM sensors s
             JOIN parking_spots ps ON ps.id = s.spot_id
             ORDER BY s.id ASC"
        );
        return $stmt->fetchAll();
    }

    public function getStats(): array
    {
        $stmt = $this->db->query(
            "SELECT
             COUNT(*) as total,
             SUM(status='online') as online,
             SUM(status='offline') as offline,
             SUM(status='error') as error_count
             FROM sensors"
        );
        return $stmt->fetch();
    }

    public function ping(int $id): void
    {
        $this->db->prepare(
            "UPDATE sensors SET last_ping=NOW(), status='online' WHERE id=?"
        )->execute([$id]);
    }

    public function getBySpot(int $spotId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM sensors WHERE spot_id=?");
        $stmt->execute([$spotId]);
        return $stmt->fetchAll();
    }
}