<?php

require_once BASE_PATH . '/models/Sensor.php';

class HealthMonitorService
{
    private Sensor $sensorModel;

    public function __construct()
    {
        $this->sensorModel = new Sensor();
    }

    public function getSystemHealth(): array
    {
        $sensors = $this->sensorModel->getAll();
        $stats   = $this->sensorModel->getStats();

        $db = Database::getInstance();

        $users = (int)$db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $spots = (int)$db->query("SELECT COUNT(*) FROM parking_spots WHERE status='active'")->fetchColumn();
        $todayRes = $db->prepare("SELECT COUNT(*) FROM reservations WHERE DATE(created_at)=CURDATE()");
        $todayRes->execute();
        $todayBookings = (int)$todayRes->fetchColumn();

        return [
            'sensors'       => $sensors,
            'sensor_stats'  => $stats,
            'total_users'   => $users,
            'active_spots'  => $spots,
            'today_bookings'=> $todayBookings,
            'server_time'   => date('Y-m-d H:i:s'),
            'php_version'   => PHP_VERSION,
            'db_status'     => 'Connected',
        ];
    }
}