<?php

require_once BASE_PATH . '/core/Model.php';

class Vehicle extends Model
{
    protected string $table = 'vehicles';

    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM vehicles WHERE user_id = ? ORDER BY id DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO vehicles (user_id, plate_number, make, model, color, year, is_ev)
             VALUES (:user_id,:plate_number,:make,:model,:color,:year,:is_ev)"
        );
        $stmt->execute([
            ':user_id'      => $data['user_id'],
            ':plate_number' => $data['plate_number'],
            ':make'         => $data['make'] ?? null,
            ':model'        => $data['model'] ?? null,
            ':color'        => $data['color'] ?? null,
            ':year'         => $data['year'] ?? null,
            ':is_ev'        => $data['is_ev'] ?? 0,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE vehicles SET plate_number=:plate_number, make=:make, model=:model,
             color=:color, year=:year, is_ev=:is_ev WHERE id=:id AND user_id=:user_id"
        );
        return $stmt->execute([
            ':plate_number' => $data['plate_number'],
            ':make'         => $data['make'] ?? null,
            ':model'        => $data['model'] ?? null,
            ':color'        => $data['color'] ?? null,
            ':year'         => $data['year'] ?? null,
            ':is_ev'        => $data['is_ev'] ?? 0,
            ':id'           => $id,
            ':user_id'      => $data['user_id'],
        ]);
    }

    public function deleteForUser(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM vehicles WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }
}