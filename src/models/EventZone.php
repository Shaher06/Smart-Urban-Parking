<?php

require_once BASE_PATH . '/core/Model.php';

class EventZone extends Model
{
    protected string $table = 'event_zones';

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO event_zones (name, description, affected_spot_ids, start_time, end_time, locked_by, status)
             VALUES (:name,:description,:affected_spot_ids,:start_time,:end_time,:locked_by,'active')"
        );
        $stmt->execute([
            ':name'              => $data['name'],
            ':description'       => $data['description'] ?? null,
            ':affected_spot_ids' => $data['affected_spot_ids'] ?? null,
            ':start_time'        => $data['start_time'],
            ':end_time'          => $data['end_time'],
            ':locked_by'         => $data['locked_by'],
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function getActive(): array
    {
        $stmt = $this->db->query(
            "SELECT ez.*, u.name as locked_by_name
             FROM event_zones ez
             LEFT JOIN users u ON u.id = ez.locked_by
             WHERE ez.status='active'
             ORDER BY ez.start_time ASC"
        );
        return $stmt->fetchAll();
    }

    public function expire(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE event_zones SET status='expired' WHERE id=?");
        return $stmt->execute([$id]);
    }
}