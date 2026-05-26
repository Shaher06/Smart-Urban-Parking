<?php


require_once BASE_PATH . '/core/Model.php';

class ParkingSpot extends Model
{
    protected string $table = 'parking_spots';

    public function getActive(): array
    {
        $stmt = $this->db->query(
            "SELECT ps.*, u.name AS owner_name,
             COALESCE(AVG(r.rating), 0) AS avg_rating
             FROM parking_spots ps
             LEFT JOIN users u    ON u.id    = ps.owner_id
             LEFT JOIN reviews r  ON r.spot_id = ps.id
             WHERE ps.status = 'active'
             GROUP BY ps.id
             ORDER BY ps.id DESC"
        );
        return $stmt->fetchAll();
    }

    public function search(array $filters): array
    {
        $sql    = "SELECT ps.*, u.name AS owner_name,
                   COALESCE(AVG(rv.rating), 0) AS avg_rating
                   FROM parking_spots ps
                   LEFT JOIN users u    ON u.id    = ps.owner_id
                   LEFT JOIN reviews rv ON rv.spot_id = ps.id
                   WHERE ps.status = 'active'";
        $params = [];

        if (!empty($filters['city'])) {
            $sql     .= " AND ps.city LIKE ?";
            $params[] = '%' . $filters['city'] . '%';
        }
        if (!empty($filters['type'])) {
            $sql     .= " AND ps.type = ?";
            $params[] = $filters['type'];
        }
        if (!empty($filters['ev'])) {
            $sql .= " AND ps.ev_support = 1";
        }
        if (!empty($filters['max_price'])) {
            $sql     .= " AND ps.price_per_hour <= ?";
            $params[] = (float)$filters['max_price'];
        }
        if (!empty($filters['min_height'])) {
            $sql     .= " AND (ps.max_vehicle_height_m IS NULL OR ps.max_vehicle_height_m >= ?)";
            $params[] = (float)$filters['min_height'];
        }
        if (!empty($filters['min_width'])) {
            $sql     .= " AND (ps.max_vehicle_width_m IS NULL OR ps.max_vehicle_width_m >= ?)";
            $params[] = (float)$filters['min_width'];
        }
        if (!empty($filters['max_difficulty'])) {
            $sql     .= " AND ps.difficulty_rating <= ?";
            $params[] = (int)$filters['max_difficulty'];
        }
        if (!empty($filters['available_only'])) {
            $sql .= " AND ps.available_slots > 0";
        }

        $sql .= " GROUP BY ps.id ORDER BY ps.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getByOwner(int $ownerId): array
    {
        $stmt = $this->db->prepare(
            "SELECT ps.*,
                    COALESCE(AVG(r.rating), 0) as avg_rating,
                    COUNT(r.id) as review_count
             FROM parking_spots ps
             LEFT JOIN reviews r ON r.spot_id = ps.id
             WHERE ps.owner_id = ?
             GROUP BY ps.id
             ORDER BY ps.id DESC"
        );
        $stmt->execute([$ownerId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO parking_spots
             (owner_id, name, address, city, latitude, longitude, type, price_per_hour,
              total_slots, available_slots, ev_support, status, description,
              max_vehicle_height_m, max_vehicle_width_m, difficulty_rating)
             VALUES
             (:owner_id, :name, :address, :city, :latitude, :longitude, :type, :price_per_hour,
              :total_slots, :available_slots, :ev_support, :status, :description,
              :max_vehicle_height_m, :max_vehicle_width_m, :difficulty_rating)"
        );
        $stmt->execute([
            ':owner_id'        => $data['owner_id'],
            ':name'            => $data['name'],
            ':address'         => $data['address'],
            ':city'            => $data['city'],
            ':latitude'        => $data['latitude'] ?? null,
            ':longitude'       => $data['longitude'] ?? null,
            ':type'            => $data['type'] ?? 'public',
            ':price_per_hour'  => $data['price_per_hour'],
            ':total_slots'     => $data['total_slots'] ?? 1,
            ':available_slots' => $data['total_slots'] ?? 1,
            ':ev_support'      => $data['ev_support'] ?? 0,
            ':status'          => $data['status'] ?? 'active',
            ':description'     => $data['description'] ?? null,
            ':max_vehicle_height_m' => $data['max_vehicle_height_m'] ?? null,
            ':max_vehicle_width_m'  => $data['max_vehicle_width_m'] ?? null,
            ':difficulty_rating'    => $data['difficulty_rating'] ?? 3,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE parking_spots SET
             name=:name, address=:address, city=:city, latitude=:latitude,
             longitude=:longitude, type=:type, price_per_hour=:price_per_hour,
             total_slots=:total_slots, available_slots=:available_slots,
             ev_support=:ev_support, status=:status, description=:description,
             max_vehicle_height_m=:max_vehicle_height_m, max_vehicle_width_m=:max_vehicle_width_m,
             difficulty_rating=:difficulty_rating
             WHERE id=:id AND owner_id=:owner_id"
        );
        return $stmt->execute([
            ':name'            => $data['name'],
            ':address'         => $data['address'],
            ':city'            => $data['city'],
            ':latitude'        => $data['latitude'] ?? null,
            ':longitude'       => $data['longitude'] ?? null,
            ':type'            => $data['type'],
            ':price_per_hour'  => $data['price_per_hour'],
            ':total_slots'     => $data['total_slots'],
            ':available_slots' => $data['available_slots'],
            ':ev_support'      => $data['ev_support'] ?? 0,
            ':status'          => $data['status'],
            ':description'     => $data['description'] ?? null,
            ':max_vehicle_height_m' => $data['max_vehicle_height_m'] ?? null,
            ':max_vehicle_width_m'  => $data['max_vehicle_width_m'] ?? null,
            ':difficulty_rating'    => $data['difficulty_rating'] ?? 3,
            ':id'              => $id,
            ':owner_id'        => $data['owner_id'],
        ]);
    }

    public function decrementSlot(int $id): void
    {
        $this->db->prepare(
            "UPDATE parking_spots
             SET available_slots = GREATEST(available_slots - 1, 0)
             WHERE id = ?"
        )->execute([$id]);
    }

    public function incrementSlot(int $id): void
    {
        $this->db->prepare(
            "UPDATE parking_spots
             SET available_slots = LEAST(available_slots + 1, total_slots)
             WHERE id = ?"
        )->execute([$id]);
    }

    public function getNearby(float $lat, float $lng, float $radius = 10): array
    {
        $stmt = $this->db->prepare(
            "SELECT *,
             (6371 * acos(
                cos(radians(?)) * cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude))
             )) AS distance
             FROM parking_spots
             WHERE status = 'active'
             HAVING distance < ?
             ORDER BY distance ASC
             LIMIT 20"
        );
        $stmt->execute([$lat, $lng, $lat, $radius]);
        return $stmt->fetchAll();
    }

    /**
     * getNearbyAlternatives() — suggest alternative spots when one is full.
     *
     * FIX: This method was missing — called by BookingService::book() when
     *      available_slots = 0, to give the driver other options.
     *
     * SRS Function: Nearby Alternative Suggestion (Function #2)
     *
     * Returns up to 5 active spots in the same city (excluding the full one),
     * ordered by available slots descending then price ascending.
     *
     * @param int    $excludeSpotId  The full spot — excluded from results
     * @param string $city           City to search in
     * @return array
     */
    public function getNearbyAlternatives(int $excludeSpotId, string $city): array
    {
        $stmt = $this->db->prepare(
            "SELECT id, name, city, address, price_per_hour,
                    available_slots, total_slots,
                    ROUND((available_slots / GREATEST(total_slots, 1)) * 100, 1) AS availability_pct
             FROM parking_spots
             WHERE status        = 'active'
               AND city          = ?
               AND id           != ?
               AND available_slots > 0
             ORDER BY available_slots DESC, price_per_hour ASC
             LIMIT 5"
        );
        $stmt->execute([$city, $excludeSpotId]);
        return $stmt->fetchAll();
    }
}