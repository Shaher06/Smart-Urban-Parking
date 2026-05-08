<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/models/Review.php';
require_once BASE_PATH . '/models/ParkingSpot.php';

class ReviewController extends Controller
{
    private Review      $reviewModel;
    private ParkingSpot $spotModel;

    public function __construct()
    {
        parent::__construct();
        $this->reviewModel = new Review();
        $this->spotModel   = new ParkingSpot();
    }

    public function driverReviews(): void
    {
        $this->requireRole('driver');
        $reviews = $this->reviewModel->getByUser(current_user_id());
        $spots   = $this->spotModel->getActive();
        $this->render('driver/reviews', ['reviews' => $reviews, 'spots' => $spots]);
    }

    public function addReview(): void
    {
        $this->requireRole('driver');
        if ($this->isPost()) {
            $spotId = (int)$this->post('spot_id');
            if ($this->reviewModel->hasReviewed(current_user_id(), $spotId)) {
                set_flash('error', 'You have already reviewed this spot.');
                $this->redirect('?page=driver-reviews');
                return;
            }
            $this->reviewModel->create([
                'user_id'        => current_user_id(),
                'spot_id'        => $spotId,
                'reservation_id' => $this->post('reservation_id') ?: null,
                'rating'         => (int)$this->post('rating', 3),
                'comment'        => $this->post('comment'),
            ]);
            set_flash('success', 'Review submitted.');
        }
        $this->redirect('?page=driver-reviews');
    }

    public function ownerReviews(): void
    {
        $this->requireRole('owner');
        $reviews = $this->reviewModel->getForOwnerSpots(current_user_id());
        $this->render('owner/reviews', ['reviews' => $reviews]);
    }
}