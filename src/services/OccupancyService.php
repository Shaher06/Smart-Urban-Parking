<?php
/**
 * OCCUPANCY SERVICE — Real-Time Occupancy Predictor (Simulated)
 *
 * SRS Function: Real-Time Occupancy Predictor
 *
 * In production: uses IoT sensor data + ML model.
 * Here: simulates occupancy prediction using historical booking patterns
 * stored in the reservations table.
 */

class OccupancyService
{
    private PDO $db;

    public function __construct()
    {
        // PATTERN: Singleton — using the single Database instance
        $this->db = Database::getInstance();
    }

    /**
     * Predict occupancy % for a spot at a given hour.
     * Uses past bookings for the same hour-of-week as a signal.
     *
     * @param int    $spotId
     * @param string $datetime  Target datetime to predict
     * @return array            ['occupancy_pct' => float, 'prediction' => string, 'confidence' => string]
     */
    public function predictOccupancy(int $spotId, string $datetime): array
    {
        $dayOfWeek = date('N', strtotime($datetime)); // 1=Mon … 7=Sun
        $hour      = (int) date('G', strtotime($datetime));

        // Count historical bookings at this spot during the same hour-of-week
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as cnt
             FROM reservations
             WHERE spot_id = ?
               AND DAYOFWEEK(start_time) = ?
               AND HOUR(start_time) = ?
               AND status NOT IN ('cancelled')"
        );
        $stmt->execute([$spotId, $dayOfWeek, $hour]);
        $historical = (int) $stmt->fetchColumn();

        // Get total slots for the spot
        $stmt2 = $this->db->prepare("SELECT total_slots FROM parking_spots WHERE id = ?");
        $stmt2->execute([$spotId]);
        $totalSlots = (int) ($stmt2->fetchColumn() ?: 1);

        // Simple simulation: cap at realistic maximum
        $predicted     = min($historical * 15, 100); // scale up historical count
        $occupancyPct  = min(100, $predicted);

        $level = match(true) {
            $occupancyPct >= 80 => 'Very Busy',
            $occupancyPct >= 50 => 'Moderately Busy',
            $occupancyPct >= 25 => 'Light Traffic',
            default             => 'Usually Free',
        };

        return [
            'spot_id'        => $spotId,
            'datetime'       => $datetime,
            'occupancy_pct'  => $occupancyPct,
            'prediction'     => $level,
            'confidence'     => $historical > 5 ? 'High' : 'Low (few data points)',
            'total_slots'    => $totalSlots,
        ];
    }

    /**
     * Get current live occupancy for all active spots.
     */
    public function getLiveOccupancy(): array
    {
        $stmt = $this->db->query(
            "SELECT ps.id, ps.name, ps.city, ps.total_slots, ps.available_slots,
             ROUND(((ps.total_slots - ps.available_slots) / ps.total_slots) * 100, 1) as occupancy_pct
             FROM parking_spots ps
             WHERE ps.status = 'active'
             ORDER BY occupancy_pct DESC"
        );
        return $stmt->fetchAll();
    }

    /**
     * Suggest peak-hour booking avoidance windows.
     */
    public function getSuggestedWindows(int $spotId): array
    {
        $suggestions = [];
        $hours = [7, 8, 9, 12, 13, 17, 18, 19]; // typical busy hours

        foreach ($hours as $h) {
            $dt   = date('Y-m-d') . sprintf(' %02d:00:00', $h);
            $pred = $this->predictOccupancy($spotId, $dt);
            if ($pred['occupancy_pct'] < 50) {
                $suggestions[] = [
                    'hour'    => sprintf('%02d:00', $h),
                    'status'  => $pred['prediction'],
                    'pct'     => $pred['occupancy_pct'],
                ];
            }
        }

        return $suggestions;
    }
}