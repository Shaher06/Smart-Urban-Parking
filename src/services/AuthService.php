<?php

require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/AuditLog.php';

class AuthService
{
    private User     $userModel;
    private AuditLog $auditLog;

    public function __construct()
    {
        $this->userModel = new User();
        $this->auditLog  = new AuditLog();
    }

    public function login(string $email, string $password): array|false
    {
        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            return false;
        }
        if (!password_verify($password, $user['password'])) {
            return false;
        }
        if ($user['status'] !== 'active') {
            return false;
        }
        login_user($user);
        $this->auditLog->log($user['id'], 'login', "User {$user['email']} logged in.");
        return $user;
    }

    public function register(array $data): int|false
    {
        $existing = $this->userModel->findByEmail($data['email']);
        if ($existing) {
            return false;
        }
        $id = $this->userModel->create($data);
        $this->auditLog->log($id, 'register', "New user registered: {$data['email']}");
        return $id;
    }

    public function logout(): void
    {
        $userId = current_user_id();
        $this->auditLog->log($userId, 'logout', 'User logged out.');
        logout_user();
    }
}