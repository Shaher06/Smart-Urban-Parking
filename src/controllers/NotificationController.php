<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/models/Notification.php';

class NotificationController extends Controller
{
    private Notification $notificationModel;

    public function __construct()
    {
        parent::__construct();
        $this->notificationModel = new Notification();
    }

    public function list(): void
    {
        $this->requireLogin();
        $notifications = $this->notificationModel->getByUser(current_user_id());
        $this->render('notifications/list', ['notifications' => $notifications]);
    }

    public function markRead(): void
    {
        $this->requireLogin();
        $id = (int)$this->get('id');
        if ($id) {
            $this->notificationModel->markRead($id, current_user_id());
        } else {
            $this->notificationModel->markAllRead(current_user_id());
        }
        $this->redirect('?page=notifications');
    }
}