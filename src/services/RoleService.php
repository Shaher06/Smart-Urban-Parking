<?php

class RoleService
{
    public function getDashboardRedirect(string $role): string
    {
        return match($role) {
            'admin'   => '?page=admin-dashboard',
            'owner'   => '?page=owner-dashboard',
            'officer' => '?page=admin-dashboard',
            default   => '?page=driver-dashboard',
        };
    }

    public function canAccessPage(string $role, string $page): bool
    {
        $adminPages  = ['admin-dashboard','admin-users','admin-fines','admin-appeals',
                        'admin-blacklist','admin-reports','audit-logs','system-health',
                        'event-zones','emergency-override','officer-dispatch','admin-roles',
                        'owners-verification'];
        $ownerPages  = ['owner-dashboard','owner-spots','add-spot','edit-spot',
                        'owner-earnings','owner-payouts','owner-verification',
                        'owner-reviews','tax-details','owner-messages'];
        $driverPages = ['driver-dashboard','browse-spots','nearby-spots','book-spot',
                        'reservations','reservation-history','check-in-out','navigate',
                        'vehicles','fines','appeal-fine','favorites','waitlist',
                        'driver-reviews','driver-messages'];

        if ($role === 'admin' || $role === 'officer') {
            return true;
        }
        if ($role === 'owner' && in_array($page, $ownerPages, true)) {
            return true;
        }
        if ($role === 'driver' && in_array($page, $driverPages, true)) {
            return true;
        }
        return false;
    }
}