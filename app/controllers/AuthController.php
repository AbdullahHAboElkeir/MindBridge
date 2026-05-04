<?php
class AuthController extends Controller
{
    public function index(): void
    {
        $this->redirect($this->config['app']['base_url'] . '?controller=auth&action=login');
    }

    public function login(): void
    {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            if ($this->auth->login($email, $password)) {
                $this->redirect($this->config['app']['base_url'] . '?controller=dashboard&action=index');
            }
            $error = 'Invalid credentials. Please try again.';
        }
        $this->view->render('auth/login', ['error' => $error]);
    }

    public function register(): void
    {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $email = trim($_POST['email'] ?? '');
            if ($userModel->findByEmail($email)) {
                $error = 'Email already registered.';
            } else {
                $userId = 0;
                try {
                    $role = in_array($_POST['role'] ?? 'patient', ['patient', 'therapist', 'admin'], true) ? $_POST['role'] : 'patient';
                    $userId = $userModel->create([
                        'name' => trim($_POST['name'] ?? ''),
                        'email' => $email,
                        'password' => $_POST['password'] ?? '',
                        'role' => $role,
                    ]);
                    if ($role === 'patient') {
                        (new Patient())->createProfile($userId, ['timezone' => 'UTC', 'preferences' => '', 'intake_status' => 'pending']);
                    }
                    if ($role === 'therapist') {
                        (new Therapist())->createProfile($userId, ['specialization' => 'General', 'license_number' => '', 'availability' => 'weekdays', 'rating' => 0]);
                    }
                    $this->redirect($this->config['app']['base_url'] . '?controller=auth&action=login');
                    return;
                } catch (Exception $ex) {
                    if ($userId > 0) {
                        (new User())->delete($userId);
                    }
                    $error = 'Registration failed. Please try again later.';
                }
            }
        }
        $this->view->render('auth/register', ['error' => $error]);
    }

    public function logout(): void
    {
        $this->auth->logout();
        $this->redirect($this->config['app']['base_url'] . '?controller=auth&action=login');
    }
}
