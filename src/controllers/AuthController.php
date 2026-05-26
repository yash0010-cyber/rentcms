<?php

class AuthController extends BaseController {
    private Auth $auth;

    public function __construct() {
        $this->auth = new Auth();
    }

    public function showLogin(): void {
        $this->render('auth/login');
    }

    public function login(): void {
        $this->requireCsrfToken();

        $email = Validator::sanitizeEmail($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $result = $this->auth->login($email, $password, isset($_POST['remember']));
        if ($result['success']) {
            $this->setFlash('success', $result['message']);
            $this->redirect('/dashboard');
        }

        $this->setFlash('danger', $result['message']);
        $this->redirect('/login');
    }

    public function showRegister(): void {
        $this->render('auth/register');
    }

    public function register(): void {
        $this->requireCsrfToken();

        $role = $_POST['role'] ?? 'tenant';
        $role = in_array($role, ['tenant', 'owner'], true) ? $role : 'tenant';

        $data = [
            'username' => Validator::sanitizeString($_POST['username'] ?? ''),
            'full_name' => Validator::sanitizeString($_POST['full_name'] ?? ''),
            'email' => Validator::sanitizeEmail($_POST['email'] ?? ''),
            'phone' => Validator::sanitizeString($_POST['phone'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'role' => $role,
        ];

        $result = $this->auth->register(
            $data['username'],
            $data['full_name'],
            $data['email'],
            $data['password'],
            $data['phone'],
            $data['role']
        );

        if ($result['success']) {
            $this->setFlash('success', $result['message']);
            $this->redirect('/login');
        }

        $this->setFlash('danger', $result['message']);
        $this->redirect('/register');
    }

    public function logout(): void {
        $this->auth->logout();
        $this->redirect('/');
    }

    public function showForgotPassword(): void {
        $this->render('auth/forgot');
    }

    public function sendForgotPassword(): void {
        $this->requireCsrfToken();
        $email = Validator::sanitizeEmail($_POST['email'] ?? '');
        $result = $this->auth->forgotPassword($email);
        $this->setFlash($result['success'] ? 'success' : 'danger', $result['message']);
        $this->redirect('/forgot-password');
    }

    public function showResetPassword(): void {
        $token = $_GET['token'] ?? '';
        $this->render('auth/reset', ['token' => $token]);
    }

    public function resetPassword(): void {
        $this->requireCsrfToken();
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';

        $result = $this->auth->resetPassword($token, $password);
        $this->setFlash($result['success'] ? 'success' : 'danger', $result['message']);
        $this->redirect('/login');
    }

    public function verifyEmail(): void {
        $token = $_GET['token'] ?? '';
        $result = $this->auth->verifyEmail($token);
        $this->setFlash($result['success'] ? 'success' : 'danger', $result['message']);
        $this->redirect('/login');
    }

    public function dashboard(): void {
        Auth::requireLogin();

        if (Auth::isAdmin()) {
            $this->redirect('/admin/dashboard');
        }

        if (Auth::isOwner()) {
            $this->redirect('/owner/dashboard');
        }

        $this->redirect('/tenant/dashboard');
    }

    public function showAdminLogin(): void {
        $this->render('auth/admin-login');
    }

    public function adminLogin(): void {
        $this->requireCsrfToken();

        $email = Validator::sanitizeEmail($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $result = $this->auth->login($email, $password, false);

        if ($result['success'] && ($result['user']['role'] ?? '') === 'admin') {
            $this->setFlash('success', 'Welcome back, admin.');
            $this->redirect('/admin/dashboard');
        }

        if ($result['success']) {
            $this->auth->logout();
        }

        $this->setFlash('danger', 'Admin credentials required.');
        $this->redirect('/admin/login');
    }
}

?>
