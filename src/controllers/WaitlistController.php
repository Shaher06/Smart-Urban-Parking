<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/models/Waitlist.php';
require_once BASE_PATH . '/models/ParkingSpot.php';

class WaitlistController extends Controller
{
    private Waitlist    $waitlistModel;
    private ParkingSpot $spotModel;

    public function __construct()
    {
        parent::__construct();
        $this->waitlistModel = new Waitlist();
        $this->spotModel     = new ParkingSpot();
    }

    public function waitlist(): void
    {
        $this->requireRole('driver');
        $items = $this->waitlistModel->getByUser(current_user_id());
        $spots = $this->spotModel->getActive();
        $this->render('driver/waitlist', ['items' => $items, 'spots' => $spots]);
    }

    public function joinWaitlist(): void
    {
        $this->requireRole('driver');
        if ($this->isPost()) {
            $spotId = (int)$this->post('spot_id');
            $start  = $this->post('requested_start');
            $end    = $this->post('requested_end');

            if ($this->waitlistModel->isAlreadyWaiting(current_user_id(), $spotId)) {
                set_flash('error', 'Already on waitlist for this spot.');
                $this->redirect('?page=waitlist');
                return;
            }

            $this->waitlistModel->add(current_user_id(), $spotId, $start, $end);
            set_flash('success', 'Added to waitlist.');
        }
        $this->redirect('?page=waitlist');
    }

    public function leaveWaitlist(): void
    {
        $this->requireRole('driver');
        $id = (int)$this->get('id');
        $this->waitlistModel->cancel($id, current_user_id());
        set_flash('success', 'Removed from waitlist.');
        $this->redirect('?page=waitlist');
    }
}