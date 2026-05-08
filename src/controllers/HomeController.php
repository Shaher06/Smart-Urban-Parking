<?php

require_once BASE_PATH . '/core/Controller.php';

class HomeController extends Controller
{
    /**
     * Public landing page (no login required).
     * Logged-in users are redirected to their dashboard.
     */
    public function landing(): void
    {
        if (is_logged_in()) {
            $role     = current_role();
            $redirect = match($role) {
                'admin', 'officer' => '?page=admin-dashboard',
                'owner'            => '?page=owner-dashboard',
                default            => '?page=driver-dashboard',
            };
            $this->redirectTo(BASE_URL . '/index.php' . $redirect);
        }

        $this->render('home/landing');
    }
}

