<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/models/Vehicle.php';
require_once BASE_PATH . '/models/User.php';

class VehicleController extends Controller
{
    private Vehicle $vehicleModel;

    public function __construct()
    {
        parent::__construct();
        $this->vehicleModel = new Vehicle();
    }

    public function vehicles(): void
    {
        $this->requireRole('driver');
        $vehicles = $this->vehicleModel->getByUser(current_user_id());
        $this->render('driver/vehicles', [
            'vehicles'           => $vehicles,
            'default_vehicle_id' => (int) (current_user()['default_vehicle_id'] ?? 0),
        ]);
    }

    public function addVehicle(): void
    {
        $this->requireRole('driver');
        if ($this->isPost()) {
            $this->vehicleModel->create([
                'user_id'      => current_user_id(),
                'plate_number' => $this->post('plate_number'),
                'make'         => $this->post('make'),
                'model'        => $this->post('model'),
                'color'        => $this->post('color'),
                'year'         => $this->post('year') ?: null,
                'is_ev'        => $this->post('is_ev') ? 1 : 0,
            ]);
            set_flash('success', 'Vehicle added.');
        }
        $this->redirect('?page=vehicles');
    }

    public function editVehicle(): void
    {
        $this->requireRole('driver');
        $id = (int)$this->get('id');
        if ($this->isPost()) {
            $this->vehicleModel->update($id, [
                'user_id'      => current_user_id(),
                'plate_number' => $this->post('plate_number'),
                'make'         => $this->post('make'),
                'model'        => $this->post('model'),
                'color'        => $this->post('color'),
                'year'         => $this->post('year') ?: null,
                'is_ev'        => $this->post('is_ev') ? 1 : 0,
            ]);
            set_flash('success', 'Vehicle updated.');
            $this->redirect('?page=vehicles');
        }
        $vehicle  = $this->vehicleModel->findById($id);
        $vehicles = $this->vehicleModel->getByUser(current_user_id());
        $this->render('driver/vehicles', [
            'vehicles'           => $vehicles,
            'edit_vehicle'       => $vehicle,
            'default_vehicle_id' => (int) (current_user()['default_vehicle_id'] ?? 0),
        ]);
    }

    public function deleteVehicle(): void
    {
        $this->requireRole('driver');
        $id = (int)$this->get('id');
        $this->vehicleModel->deleteForUser($id, current_user_id());
        $u = new User();
        if ((int) (current_user()['default_vehicle_id'] ?? 0) === $id) {
            $u->update(current_user_id(), ['default_vehicle_id' => null]);
            $_SESSION['user']['default_vehicle_id'] = null;
        }
        set_flash('success', 'Vehicle removed.');
        $this->redirect('?page=vehicles');
    }

    /** Set preferred vehicle for new bookings (SRS: switch / change vehicle). */
    public function setDefaultVehicle(): void
    {
        $this->requireRole('driver');
        $id = (int)$this->get('id');
        $v   = $this->vehicleModel->findById($id);
        if (!$v || (int)$v['user_id'] !== current_user_id()) {
            set_flash('error', 'Vehicle not found.');
            $this->redirect('?page=vehicles');
            return;
        }
        (new User())->update(current_user_id(), ['default_vehicle_id' => $id]);
        $_SESSION['user']['default_vehicle_id'] = $id;
        set_flash('success', 'Default vehicle saved for bookings.');
        $this->redirect('?page=vehicles');
    }
}