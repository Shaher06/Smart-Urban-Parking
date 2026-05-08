<?php

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/constants.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/core/View.php';
require_once BASE_PATH . '/core/Session.php';
require_once BASE_PATH . '/helpers/auth_helper.php';
require_once BASE_PATH . '/helpers/url_helper.php';
require_once BASE_PATH . '/helpers/flash_helper.php';

class Controller
{
    protected View    $view;
    protected Session $session;

    public function __construct()
    {
        $this->view    = new View();
        $this->session = new Session();
    }

    protected function render(string $template, array $data = []): void
    {
        $this->view->render($template, $data);
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . BASE_URL . '/' . ltrim($url, '/'));
        exit;
    }

    protected function redirectTo(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function requireLogin(): void
    {
        if (!is_logged_in()) {
            set_flash('error', 'Please login to continue.');
            $this->redirect('?page=login');
        }
    }

    protected function requireRole(string $role): void
    {
        $this->requireLogin();
        if (current_role() !== $role) {
            $this->render('errors/403');
            exit;
        }
    }

    protected function requireRoles(array $roles): void
    {
        $this->requireLogin();
        if (!in_array(current_role(), $roles, true)) {
            $this->render('errors/403');
            exit;
        }
    }

    protected function db(): PDO
    {
        return Database::getInstance();
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function post(string $key, mixed $default = ''): mixed
    {
        return $_POST[$key] ?? $default;
    }

    protected function get(string $key, mixed $default = ''): mixed
    {
        return $_GET[$key] ?? $default;
    }

    protected function e(mixed $value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}