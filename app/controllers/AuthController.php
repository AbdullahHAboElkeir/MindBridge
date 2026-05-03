<?php
// app/controllers/AuthController.php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Role.php';

class AuthController extends Controller {
    public function login() {
        if ($this->isLoggedIn()) {
            $this->redirect('/dashboard');
        }
        $this->render('auth/login');
    }

    public function authenticate() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Please fill in all fields';
            $this->redirect('/login');
        }

        $userModel = new User();
        $user = $userModel->authenticate($email, $password);

        if ($user) {
            $userWithRole = $userModel->getWithRole($user['id']);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user'] = $userWithRole;
            $this->redirect('/dashboard');
        } else {
            $_SESSION['error'] = 'Invalid credentials';
            $this->redirect('/login');
        }
    }

    public function register() {
        $roleModel = new Role();
        $roles = $roleModel->findAll();
        $this->render('auth/register', ['roles' => $roles]);
    }

    public function store() {
        $data = [
            'username' => $_POST['username'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT),
            'role_id' => $_POST['role_id'] ?? '',
            'first_name' => $_POST['first_name'] ?? '',
            'last_name' => $_POST['last_name'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'date_of_birth' => $_POST['date_of_birth'] ?? null,
            'gender' => $_POST['gender'] ?? null,
            'address' => $_POST['address'] ?? ''
        ];

        // Basic validation
        if (empty($data['username']) || empty($data['email']) || empty($data['password']) || empty($data['role_id'])) {
            $_SESSION['error'] = 'Please fill in required fields';
            $this->redirect('/register');
        }

        $userModel = new User();
        $userId = $userModel->create($data);

        if ($userId) {
            // Create role-specific record
            $roleModel = new Role();
            $role = $roleModel->find($data['role_id']);

            if ($role['name'] === 'patient') {
                require_once __DIR__ . '/../models/Patient.php';
                $patientModel = new Patient();
                $patientModel->create(['user_id' => $userId]);
            } elseif ($role['name'] === 'therapist') {
                require_once __DIR__ . '/../models/Therapist.php';
                $therapistModel = new Therapist();
                $therapistModel->create([
                    'user_id' => $userId,
                    'license_number' => $_POST['license_number'] ?? '',
                    'specialization' => $_POST['specialization'] ?? ''
                ]);
            }

            $_SESSION['success'] = 'Registration successful. Please login.';
            $this->redirect('/login');
        } else {
            $_SESSION['error'] = 'Registration failed';
            $this->redirect('/register');
        }
    }

    public function logout() {
        session_destroy();
        $this->redirect('/login');
    }
}
?>