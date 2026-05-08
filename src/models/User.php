<?php

require_once BASE_PATH . '/core/Model.php';

class User extends Model
{
    protected string $table = 'users';

    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password, phone, role, status, language)
             VALUES (:name, :email, :password, :phone, :role, :status, :language)"
        );
        $stmt->execute([
            ':name'     => $data['name'],
            ':email'    => $data['email'],
            ':password' => password_hash($data['password'], PASSWORD_BCRYPT),
            ':phone'    => $data['phone'] ?? null,
            ':role'     => $data['role'] ?? 'driver',
            ':status'   => $data['status'] ?? 'active',
            ':language' => $data['language'] ?? 'en',
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [];
        $allowed = ['name','phone','language','profile_image','status','role','default_vehicle_id'];
        foreach ($allowed as $col) {
            if (array_key_exists($col, $data)) {
                $fields[] = "{$col} = :{$col}";
                $params[":{$col}"] = $data[$col];
            }
        }
        if (empty($fields)) {
            return false;
        }
        $params[':id'] = $id;
        $sql  = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function updatePassword(int $id, string $newPassword): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([password_hash($newPassword, PASSWORD_BCRYPT), $id]);
    }

    public function searchByName(string $query): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY id DESC"
        );
        $like = '%' . $query . '%';
        $stmt->execute([$like, $like]);
        return $stmt->fetchAll();
    }

    public function getByRole(string $role): array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE role = ? ORDER BY name ASC");
        $stmt->execute([$role]);
        return $stmt->fetchAll();
    }

    public function countUnpaidFines(int $userId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM fines WHERE user_id = ? AND status = 'unpaid'"
        );
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }
}