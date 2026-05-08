<?php

require_once BASE_PATH . '/models/User.php';

class Driver extends User
{
    public function getDriverStats(int $userId): array
    {
        $db = $this->db;

        $res = $db->prepare("SELECT COUNT(*) FROM reservations WHERE user_id = ?");
        $res->execute([$userId]);
        $totalReservations = (int)$res->fetchColumn();

        $fin = $db->prepare("SELECT COUNT(*) FROM fines WHERE user_id = ? AND status = 'unpaid'");
        $fin->execute([$userId]);
        $unpaidFines = (int)$fin->fetchColumn();

        $veh = $db->prepare("SELECT COUNT(*) FROM vehicles WHERE user_id = ?");
        $veh->execute([$userId]);
        $vehicles = (int)$veh->fetchColumn();

        $fav = $db->prepare("SELECT COUNT(*) FROM favorites WHERE user_id = ?");
        $fav->execute([$userId]);
        $favorites = (int)$fav->fetchColumn();

        return compact('totalReservations', 'unpaidFines', 'vehicles', 'favorites');
    }
}