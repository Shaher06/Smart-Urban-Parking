<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/services/AuthService.php';
require_once BASE_PATH . '/services/RoleService.php';
require_once BASE_PATH . '/core/Validator.php';

class AuthController extends Controller
{
    private AuthService $authService;
    private RoleService $roleService;

    public function __construct()
    {
        parent::__construct();
        $this->authService = new AuthService();
        $this->roleService = new RoleService();
    }

    public function login(): void
    {
        if (is_logged_in()) {
            $this->redirectTo(BASE_URL . '/index.php' . $this->roleService->getDashboardRedirect(current_role()));
        }

        if ($this->isPost()) {
            $email    = trim($this->post('email'));
            $password = $this->post('password');

            $validator = new Validator();
            $validator->required('email', $email)->email('email', $email)->required('password', $password);

            if ($validator->fails()) {
                set_flash('error', $validator->firstError());
                $this->render('auth/login');
                return;
            }

            $user = $this->authService->login($email, $password);
            if (!$user) {
                set_flash('error', 'Invalid credentials or account suspended.');
                $this->render('auth/login');
                return;
            }

            $redirect = $this->roleService->getDashboardRedirect($user['role']);
            $this->redirectTo(BASE_URL . '/index.php' . $redirect);
        }

        $this->render('auth/login');
    }

    public function register(): void
    {
        if (is_logged_in()) {
            $this->redirectTo(BASE_URL . '/index.php?page=driver-dashboard');
        }

        if ($this->isPost()) {
            $name     = trim($this->post('name'));
            $email    = trim($this->post('email'));
            $password = $this->post('password');
            $confirm  = $this->post('confirm_password');
            $phone    = trim($this->post('phone'));
            $role     = $this->post('role', 'driver');

            $validator = new Validator();
            $validator->required('name', $name)
                      ->required('email', $email)->email('email', $email)
                      ->required('password', $password)->minLength('password', $password, 6)
                      ->matches('confirm_password', $confirm, $password, 'password')
                      ->inArray('role', $role, ['driver','owner']);

            if ($validator->fails()) {
                set_flash('error', $validator->firstError());
                $this->render('auth/register');
                return;
            }

            $id = $this->authService->register([
                'name' => $name, 'email' => $email,
                'password' => $password, 'phone' => $phone,
                'role' => $role,
            ]);

            if (!$id) {
                set_flash('error', 'Email already registered.');
                $this->render('auth/register');
                return;
            }

            set_flash('success', 'Registration successful. Please login.');
            $this->redirect('?page=login');
        }

        $this->render('auth/register');
    }

    public function logout(): void
    {
        $this->authService->logout();
        set_flash('success', 'Logged out successfully.');
        $this->redirect('?page=login');
    }
}