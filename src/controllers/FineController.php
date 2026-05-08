<?php
/**
 * FINE CONTROLLER — Refactored
 *
 * PATTERN: ServiceFactory used for FineService (Factory Pattern)
 * Business logic fully delegated to FineService (clean MVC separation)
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/core/ServiceFactory.php';
require_once BASE_PATH . '/models/Fine.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/core/Validator.php';

class FineController extends Controller
{
    private Fine $fineModel;

    public function __construct()
    {
        parent::__construct();
        $this->fineModel = new Fine();
    }

    /**
     * DRIVER: View my fines
     */
    public function driverFines(): void
    {
        $this->requireRole('driver');
        $fines = $this->fineModel->getByUser(current_user_id());
        $this->render('driver/fines', ['fines' => $fines]);
    }

    /**
     * DRIVER: Pay a fine
     */
    public function payFine(): void
    {
        $this->requireRole('driver');
        $id = (int)$this->get('id');

        if ($this->isPost()) {
            $method = $this->post('method', 'credit_card');

            /** @var FineService $fineService */
            $fineService = ServiceFactory::make('fine'); // PATTERN: Factory
            $result      = $fineService->payFine($id, current_user_id(), $method);

            if ($result['success']) {
                set_flash('success', 'Fine paid successfully. Reference: ' . ($result['transaction_ref'] ?? ''));
            } else {
                set_flash('error', $result['message'] ?? 'Payment failed.');
            }

            $this->redirect('?page=fines');
            return;
        }

        $fine  = $this->fineModel->findById($id);
        $fines = $this->fineModel->getByUser(current_user_id());
        $this->render('driver/fines', ['fines' => $fines, 'pay_fine' => $fine]);
    }

    /**
     * ADMIN: View all fines
     */
    public function adminFines(): void
    {
        $this->requireRoles(['admin', 'officer']);
        $fines    = $this->fineModel->getAll();
        $users    = (new User())->getByRole('driver');
        $fineService = ServiceFactory::make('fine');
        $stats    = $fineService->getFineStats();

        $this->render('admin/fines', [
            'fines' => $fines,
            'users' => $users,
            'stats' => $stats,
        ]);
    }

    /**
     * ADMIN/OFFICER: Issue a fine
     *
     * FIX: Full validation now enforced before calling FineService.
     */
    public function issueFine(): void
    {
        $this->requireRoles(['admin', 'officer']);

        if ($this->isPost()) {
            $v = new Validator();
            $v->required('user_id', $this->post('user_id'))
              ->required('amount', $this->post('amount'))
              ->positive('amount', $this->post('amount'))
              ->required('reason', $this->post('reason'));

            if ($v->fails()) {
                set_flash('error', $v->firstError());
                $this->redirect('?page=admin-fines');
                return;
            }

            /** @var FineService $fineService */
            $fineService = ServiceFactory::make('fine'); // PATTERN: Factory
            $result      = $fineService->issueFine(
                (int)$this->post('user_id'),
                (float)$this->post('amount'),
                trim($this->post('reason')),
                current_user_id(),
                $this->post('reservation_id') ? (int)$this->post('reservation_id') : null
            );

            if ($result['success']) {
                set_flash('success', "Fine #{$result['fine_id']} issued successfully.");
            } else {
                set_flash('error', $result['message'] ?? 'Failed to issue fine.');
            }
        }

        $this->redirect('?page=admin-fines');
    }

    /**
     * ADMIN: Waive a fine manually (outside of appeal workflow).
     */
    public function waiveFine(): void
    {
        $this->requireRole('admin');
        $id = (int)$this->get('id');

        if ($id) {
            $fineService = ServiceFactory::make('fine');
            $fineService->waiveFine($id, current_user_id(), 'Admin waiver');
            set_flash('success', 'Fine waived.');
        }

        $this->redirect('?page=admin-fines');
    }
}