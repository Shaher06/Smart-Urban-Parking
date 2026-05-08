<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/models/ParkingSpot.php';
require_once BASE_PATH . '/models/Favorites.php';

class SystemController extends Controller
{
    private Favorites $favoritesModel;

    public function __construct()
    {
        parent::__construct();
        $this->favoritesModel = new Favorites();
    }

    public function favorites(): void
    {
        $this->requireRole('driver');
        $favorites = $this->favoritesModel->getByUser(current_user_id());
        $this->render('driver/favorites', ['favorites' => $favorites]);
    }

    public function toggleFavorite(): void
    {
        $this->requireRole('driver');
        $spotId = (int)$this->get('spot_id');

        if ($this->favoritesModel->isFavorite(current_user_id(), $spotId)) {
            $this->favoritesModel->remove(current_user_id(), $spotId);
            set_flash('info', 'Removed from favorites.');
        } else {
            $this->favoritesModel->add(current_user_id(), $spotId);
            set_flash('success', 'Added to favorites.');
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/index.php?page=browse-spots';
        $this->redirectTo($referer);
    }
}