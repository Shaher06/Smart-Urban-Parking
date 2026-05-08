<?php

require_once BASE_PATH . '/models/User.php';

class Admin extends User
{
    public function getAdminStats(): array
    {
        $db = $this->db;

        $users = (int)$db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $spots = (int)$db->query("SELECT COUNT(*) FROM parking_spots")->fetchColumn();
        $reservations = (int)$db->query("SELECT COUNT(*) FROM reservations")->fetchColumn();

        $revenue = $db->prepare("SELECT COALESCE(SUM(amount),0) FROM payments WHERE status='completed'");
        $revenue->execute();
        $totalRevenue = (float)$revenue->fetchColumn();

        $fines   = (int)$db->query("SELECT COUNT(*) FROM fines WHERE status='unpaid'")->fetchColumn();
        $appeals = (int)$db->query("SELECT COUNT(*) FROM appeals WHERE status='pending'")->fetchColumn();

        return compact('users','spots','reservations','totalRevenue','fines','appeals');
    }
}