<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/models/Message.php';
require_once BASE_PATH . '/models/User.php';

class MessageController extends Controller
{
    private Message $messageModel;
    private User    $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->messageModel = new Message();
        $this->userModel    = new User();
    }

    public function driverMessages(): void
    {
        $this->requireRole('driver');
        $inbox = $this->messageModel->getInbox(current_user_id());
        $sent  = $this->messageModel->getSent(current_user_id());
        $owners = $this->userModel->getByRole('owner');
        $this->render('driver/messages', ['inbox' => $inbox, 'sent' => $sent, 'owners' => $owners]);
    }

    public function ownerMessages(): void
    {
        $this->requireRole('owner');
        $inbox   = $this->messageModel->getInbox(current_user_id());
        $sent    = $this->messageModel->getSent(current_user_id());
        $drivers = $this->userModel->getByRole('driver');
        $this->render('owner/messages', ['inbox' => $inbox, 'sent' => $sent, 'drivers' => $drivers]);
    }

    public function sendMessage(): void
    {
        $this->requireLogin();
        if ($this->isPost()) {
            $this->messageModel->send([
                'sender_id'   => current_user_id(),
                'receiver_id' => (int)$this->post('receiver_id'),
                'subject'     => $this->post('subject'),
                'body'        => $this->post('body'),
            ]);
            set_flash('success', 'Message sent.');
        }
        $referer = $_SERVER['HTTP_REFERER'] ?? (BASE_URL . '/index.php?page=driver-messages');
        $this->redirectTo($referer);
    }

    public function markRead(): void
    {
        $this->requireLogin();
        $id = (int)$this->get('id');
        $this->messageModel->markRead($id, current_user_id());
        $this->redirectTo($_SERVER['HTTP_REFERER'] ?? BASE_URL . '/index.php?page=driver-messages');
    }
}