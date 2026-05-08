<?php

require_once BASE_PATH . '/core/Model.php';

class PromoCode extends Model
{
    protected string $table = 'promo_codes';

    public function findByCode(string $code): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM promo_codes WHERE code = ? AND status='active'
             AND valid_from <= NOW() AND valid_until >= NOW()
             AND used_count < max_uses
             LIMIT 1"
        );
        $stmt->execute([$code]);
        return $stmt->fetch();
    }

    public function incrementUsage(int $id): void
    {
        $this->db->prepare("UPDATE promo_codes SET used_count = used_count + 1 WHERE id=?")
             ->execute([$id]);
    }

    public function getAll(): array
    {
        return $this->findAll('created_at DESC');
    }
}