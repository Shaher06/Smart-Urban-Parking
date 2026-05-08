<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/models/Appeal.php';
require_once BASE_PATH . '/models/Fine.php';
require_once BASE_PATH . '/services/AppealService.php';
require_once BASE_PATH . '/services/UploadService.php';

class AppealController extends Controller
{
    private AppealService $appealService;
    private Appeal        $appealModel;
    private Fine          $fineModel;
    private UploadService $uploadService;

    public function __construct()
    {
        parent::__construct();
        $this->appealService = new AppealService();
        $this->appealModel   = new Appeal();
        $this->fineModel     = new Fine();
        $this->uploadService = new UploadService();
    }

    public function appealFine(): void
    {
        $this->requireRole('driver');
        $fineId = (int)$this->get('id');
        $fine   = $this->fineModel->findById($fineId);

        if (!$fine || (int)$fine['user_id'] !== current_user_id()) {
            set_flash('error', 'Fine not found.');
            $this->redirect('?page=fines');
            return;
        }

        if ($this->isPost()) {
            $reason       = $this->post('reason');
            $evidencePath = null;

            if (!empty($_FILES['evidence']['name'])) {
                $evidencePath = $this->uploadService->uploadEvidence($_FILES['evidence'], current_user_id(), $fineId);
            }

            $result = $this->appealService->submit($fineId, current_user_id(), $reason, $evidencePath);

            if ($result['success']) {
                set_flash('success', 'Appeal submitted successfully.');
                $this->redirect('?page=fines');
            } else {
                set_flash('error', $result['message']);
                $this->render('driver/appeal-fine', ['fine' => $fine]);
            }
            return;
        }

        $this->render('driver/appeal-fine', ['fine' => $fine]);
    }

    public function adminAppeals(): void
    {
        $this->requireRole('admin');
        $appeals = $this->appealModel->getAll();
        $this->render('admin/appeals', ['appeals' => $appeals]);
    }

    public function reviewAppeal(): void
    {
        $this->requireRole('admin');
        if ($this->isPost()) {
            $appealId = (int)$this->post('appeal_id');
            $decision = $this->post('decision');
            $note     = $this->post('note', '');
            $result   = $this->appealService->review($appealId, current_user_id(), $decision, $note);
            if ($result['success']) {
                set_flash('success', 'Appeal reviewed.');
            } else {
                set_flash('error', $result['message']);
            }
        }
        $this->redirect('?page=admin-appeals');
    }
}