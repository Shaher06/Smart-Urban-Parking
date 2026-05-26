<?php


class NavigationService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance(); // PATTERN: Singleton
    }

    /**
     * Generate a Google Maps deep link for navigation.
     */
    public function getNavigationLink(float $lat, float $lng, string $address): string
    {
        if ($lat && $lng) {
            return "https://www.google.com/maps/dir/?api=1&destination={$lat},{$lng}&travelmode=driving";
        }
        return "https://www.google.com/maps/search/?api=1&query=" . urlencode($address);
    }

    /**
     * NEARBY ALTERNATIVE SUGGESTION
     *
     * SRS Function: Nearby Alternative Suggestion
     * When a spot is full, suggest nearby alternatives ordered by:
     * 1. Available slots (must have slots)
     * 2. Price (ascending)
     * 3. Distance (if lat/lng available)
     *
     * @param int    $excludeSpotId  The full spot — exclude from results
     * @param string $city           Filter to same city
     * @param float  $lat            Optional latitude for distance sort
     * @param float  $lng            Optional longitude for distance sort
     * @return array
     */
    public function getNearbyAlternatives(int $excludeSpotId, string $city, float $lat = 0, float $lng = 0): array
    {
        if ($lat && $lng) {
            // Distance-sorted query
            $stmt = $this->db->prepare(
                "SELECT *,
                 (6371 * acos(
                    cos(radians(?)) * cos(radians(latitude))
                    * cos(radians(longitude) - radians(?))
                    + sin(radians(?)) * sin(radians(latitude))
                 )) AS distance
                 FROM parking_spots
                 WHERE status = 'active'
                   AND available_slots > 0
                   AND id != ?
                 HAVING distance < 15
                 ORDER BY distance ASC, price_per_hour ASC
                 LIMIT 5"
            );
            $stmt->execute([$lat, $lng, $lat, $excludeSpotId]);
        } else {
            // Same-city fallback
            $stmt = $this->db->prepare(
                "SELECT * FROM parking_spots
                 WHERE status = 'active'
                   AND available_slots > 0
                   AND city = ?
                   AND id != ?
                 ORDER BY price_per_hour ASC
                 LIMIT 5"
            );
            $stmt->execute([$city, $excludeSpotId]);
        }

        return $stmt->fetchAll();
    }

    /**
     * Get estimated travel time (simulated).
     * In production: call Google Maps Distance Matrix API.
     */
    public function getEstimatedTravelTime(float $userLat, float $userLng, float $spotLat, float $spotLng): string
    {
        // Haversine distance in km
        $R    = 6371;
        $dLat = deg2rad($spotLat - $userLat);
        $dLng = deg2rad($spotLng - $userLng);
        $a    = sin($dLat/2)**2 + cos(deg2rad($userLat)) * cos(deg2rad($spotLat)) * sin($dLng/2)**2;
        $dist = $R * 2 * asin(sqrt($a));

        // Simulate: assume 30km/h average city speed
        $minutes = round(($dist / 30) * 60);

        if ($minutes < 1)  return 'Less than 1 min';
        if ($minutes < 60) return "{$minutes} min";
        $hours = floor($minutes / 60);
        $mins  = $minutes % 60;
        return "{$hours}h {$mins}min";
    }
}