<?php

require_once BASE_PATH . '/core/Model.php';

class Tax extends Model
{
    protected string $table = 'taxes';

    public function generate(int $ownerId, int $year): array
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(p.amount),0) as total
             FROM payments p
             JOIN reservations r ON r.id = p.reservation_id
             JOIN parking_spots ps ON ps.id = r.spot_id
             WHERE ps.owner_id = ? AND YEAR(p.created_at) = ? AND p.status='completed'"
        );
        $stmt->execute([$ownerId, $year]);
        $revenue = (float)$stmt->fetchColumn();

        // Use TAX_RATE constant (defined in config/app.php) — was hardcoded 0.10
        $tax = $revenue * TAX_RATE;
        $vat = $revenue * VAT_RATE;

        $ins = $this->db->prepare(
            "INSERT INTO taxes (owner_id, tax_year, total_revenue, tax_amount, vat_amount)
             VALUES (?,?,?,?,?)"
        );
        $ins->execute([$ownerId, $year, $revenue, $tax, $vat]);

        return ['revenue' => $revenue, 'tax' => $tax, 'vat' => $vat, 'year' => $year];
    }

    public function getByOwner(int $ownerId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM taxes WHERE owner_id=? ORDER BY tax_year DESC"
        );
        $stmt->execute([$ownerId]);
        return $stmt->fetchAll();
    }
}