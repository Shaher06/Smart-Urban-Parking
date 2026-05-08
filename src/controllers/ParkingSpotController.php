<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/models/ParkingSpot.php';
require_once BASE_PATH . '/models/Review.php';
require_once BASE_PATH . '/models/Favorites.php';   // now exists
require_once BASE_PATH . '/core/Validator.php';

class ParkingSpotController extends Controller
{
    private ParkingSpot $spotModel;
    private Review      $reviewModel;

    public function __construct()
    {
        parent::__construct();
        $this->spotModel   = new ParkingSpot();
        $this->reviewModel = new Review();
    }

    public function browse(): void
    {
        $this->requireRole('driver');
        $filters = [
            'city'             => $this->get('city'),
            'type'             => $this->get('type'),
            'ev'               => $this->get('ev'),
            'max_price'        => $this->get('max_price'),
            'min_height'       => $this->get('min_height'),
            'min_width'        => $this->get('min_width'),
            'max_difficulty'   => $this->get('max_difficulty'),
            'available_only'   => $this->get('available_only'),
        ];
        $spots = array_filter($filters)
            ? $this->spotModel->search($filters)
            : $this->spotModel->getActive();
        $this->render('driver/browse-spots', ['spots' => $spots, 'filters' => $filters]);
    }

    public function nearby(): void
    {
        $this->requireRole('driver');
        $lat   = (float)$this->get('lat', 40.7128);
        $lng   = (float)$this->get('lng', -74.0060);
        $spots = $this->spotModel->getNearby($lat, $lng);
        $this->render('driver/nearby-spots', ['spots' => $spots, 'lat' => $lat, 'lng' => $lng]);
    }

    public function ownerSpots(): void
    {
        $this->requireRole('owner');
        $spots = $this->spotModel->getByOwner(current_user_id());
        $this->render('owner/parking-spots', ['spots' => $spots]);
    }

    public function addSpot(): void
    {
        $this->requireRole('owner');
        if ($this->isPost()) {
            $v = new Validator();
            $v->required('name', $this->post('name'))
              ->required('address', $this->post('address'))
              ->required('city', $this->post('city'))
              ->positive('price_per_hour', $this->post('price_per_hour'));

            if ($v->fails()) {
                set_flash('error', $v->firstError());
                $this->render('owner/add-spot');
                return;
            }

            $this->spotModel->create([
                'owner_id'              => current_user_id(),
                'name'                  => $this->post('name'),
                'address'               => $this->post('address'),
                'city'                  => $this->post('city'),
                'latitude'              => $this->post('latitude') ?: null,
                'longitude'             => $this->post('longitude') ?: null,
                'type'                  => $this->post('type', 'public'),
                'price_per_hour'        => (float)$this->post('price_per_hour'),
                'total_slots'           => (int)$this->post('total_slots', 1),
                'ev_support'            => $this->post('ev_support') ? 1 : 0,
                'status'                => 'active',
                'description'           => $this->post('description'),
                'max_vehicle_height_m'  => $this->post('max_vehicle_height_m') !== ''
                    ? (float)$this->post('max_vehicle_height_m') : null,
                'max_vehicle_width_m'   => $this->post('max_vehicle_width_m') !== ''
                    ? (float)$this->post('max_vehicle_width_m') : null,
                'difficulty_rating'     => max(1, min(5, (int)$this->post('difficulty_rating', 3))),
            ]);
            set_flash('success', 'Parking spot added.');
            $this->redirect('?page=owner-spots');
        }
        $this->render('owner/add-spot');
    }

    public function editSpot(): void
    {
        $this->requireRole('owner');
        $id   = (int)$this->get('id');
        $spot = $this->spotModel->findById($id);
        if (!$spot || (int)$spot['owner_id'] !== current_user_id()) {
            set_flash('error', 'Spot not found.');
            $this->redirect('?page=owner-spots');
            return;
        }
        if ($this->isPost()) {
            $this->spotModel->update($id, [
                'owner_id'              => current_user_id(),
                'name'                  => $this->post('name'),
                'address'               => $this->post('address'),
                'city'                  => $this->post('city'),
                'latitude'              => $this->post('latitude') ?: null,
                'longitude'             => $this->post('longitude') ?: null,
                'type'                  => $this->post('type', 'public'),
                'price_per_hour'        => (float)$this->post('price_per_hour'),
                'total_slots'           => (int)$this->post('total_slots', 1),
                'available_slots'       => (int)$this->post('available_slots', 1),
                'ev_support'            => $this->post('ev_support') ? 1 : 0,
                'status'                => $this->post('status', 'active'),
                'description'           => $this->post('description'),
                'max_vehicle_height_m'  => $this->post('max_vehicle_height_m') !== ''
                    ? (float)$this->post('max_vehicle_height_m') : null,
                'max_vehicle_width_m'   => $this->post('max_vehicle_width_m') !== ''
                    ? (float)$this->post('max_vehicle_width_m') : null,
                'difficulty_rating'     => max(1, min(5, (int)$this->post('difficulty_rating', 3))),
            ]);
            set_flash('success', 'Spot updated.');
            $this->redirect('?page=owner-spots');
        }
        $this->render('owner/edit-spot', ['spot' => $spot]);
    }

    public function deleteSpot(): void
    {
        $this->requireRole('owner');
        $id   = (int)$this->get('id');
        $spot = $this->spotModel->findById($id);
        if ($spot && (int)$spot['owner_id'] === current_user_id()) {
            $this->spotModel->deleteById($id);
            set_flash('success', 'Spot deleted.');
        }
        $this->redirect('?page=owner-spots');
    }
}