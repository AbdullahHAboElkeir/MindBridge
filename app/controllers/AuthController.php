<?php

/**
 * Controller: Auth
 * Handles login, registration, logout.
 */
class AuthController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = $this->model('User');
    }

    /** GET /auth/login */
    public function index(): void
    {
        $this->login();
    }

    public function login(): void
    {
        Middleware::guestOnly();
        $pageTitle = 'Sign In';
        $this->view('auth.login', compact('pageTitle'));
    }

    /** POST /auth/login */
    public function doLogin(): void
    {
        Middleware::guestOnly();

        if (!$this->isPost()) {
            $this->redirect('auth/login');
        }

        $email    = $this->post('email', '');
        $password = $this->post('password', '');
        $errors   = [];

        if (empty($email))    $errors[] = 'Email is required.';
        if (empty($password)) $errors[] = 'Password is required.';

        if (empty($errors)) {
            $user = $this->userModel->authenticate($email, $password);

            if ($user) {
                // Regenerate session id to prevent fixation and persist auth state
                Session::regenerate();

                // Set session
                Session::set('user_id',    (int)$user['id']);
                Session::set('email',      $user['email']);
                Session::set('name',       $user['name'] ?? trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')));
                Session::set('first_name', $user['first_name'] ?? '');
                Session::set('last_name',  $user['last_name'] ?? '');
                Session::set('role',       $user['role']);
                Session::set('avatar',     $user['avatar'] ?? null);

                $this->auditLog('login', 'users', 'Login successful');
                Session::flash('success', 'Welcome back, ' . ($user['first_name'] ?? $user['email']) . '!');
                $this->redirect('dashboard');
            } else {
                $errors[] = 'Invalid email or password. Please try again.';
            }
        }

        $pageTitle = 'Sign In';
        $this->view('auth.login', compact('pageTitle', 'email', 'errors'));
    }

    /** GET /auth/register */
    public function register(): void
    {
        Middleware::guestOnly();
        $pageTitle = 'Create Account';
        $this->view('auth.register', compact('pageTitle'));
    }

    /** POST /auth/register */
    public function doRegister(): void
    {
        Middleware::guestOnly();

        if (!$this->isPost()) {
            $this->redirect('auth/register');
        }

        $data = [
            'first_name'      => $this->post('first_name', ''),
            'last_name'       => $this->post('last_name', ''),
            'email'           => $this->post('email', ''),
            'password'        => $this->post('password', ''),
            'password_confirm'=> $this->post('password_confirm', ''),
            'role'            => $this->post('role', 'patient'),
            'gender'          => $this->post('gender', ''),
            'phone'           => $this->post('phone', ''),
            'timezone'        => $this->post('timezone', 'UTC'),
            // Therapist extras
            'license_number'  => $this->post('license_number', ''),
            'specializations' => $this->post('specializations', ''),
            'languages'       => $this->post('languages', 'English'),
            'bio'             => $this->post('bio', ''),
            'years_experience'=> $this->post('years_experience', 0),
            'session_rate'    => $this->post('session_rate', 0),
        ];

        $errors = $this->validateRegistration($data);

        if (empty($errors)) {
            $userId = $this->userModel->register($data);

            if ($userId) {
                $this->auditLog('register', 'users', 'New ' . $data['role'] . ' registered: ' . $data['email']);
                Session::flash('success', 'Account created! Please sign in.');
                $this->redirect('auth/login');
            } else {
                $errors[] = 'Registration failed. Please try again.';
            }
        }

        $pageTitle = 'Create Account';
        $this->view('auth.register', compact('pageTitle', 'data', 'errors'));
    }

    /** GET /auth/logout */
    public function logout(): void
    {
        $this->auditLog('logout', 'users', 'User logged out');
        Session::destroy();
        Session::start();
        Session::flash('success', 'You have been logged out.');
        $this->redirect('auth/login');
    }

    /** Validate registration data */
    private function validateRegistration(array $d): array
    {
        $errors = [];
        if (empty($d['first_name'])) $errors[] = 'First name is required.';
        if (empty($d['last_name']))  $errors[] = 'Last name is required.';
        if (empty($d['email']) || !filter_var($d['email'], FILTER_VALIDATE_EMAIL))
            $errors[] = 'A valid email address is required.';
        if (strlen($d['password']) < 8)
            $errors[] = 'Password must be at least 8 characters.';
        if ($d['password'] !== $d['password_confirm'])
            $errors[] = 'Passwords do not match.';
        if (!in_array($d['role'], ['patient', 'therapist']))
            $errors[] = 'Invalid role selected.';
        if ($this->userModel->emailExists($d['email']))
            $errors[] = 'This email is already registered.';
        if ($d['role'] === 'therapist' && empty($d['license_number']))
            $errors[] = 'License number is required for therapists.';
        return $errors;
    }
}
