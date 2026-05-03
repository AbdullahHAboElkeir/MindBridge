<?php
// app/controllers/UserController.php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Role.php';

class UserController extends Controller {
    public function index() {
        $this->requireLogin();
        $this->requireRole('admin');

        $userModel = new User();
        $users = $userModel->getAllWithRoles();

        $this->render('users/index', ['users' => $users]);
    }

    public function create() {
        $this->requireLogin();
        $this->requireRole('admin');

        $roleModel = new Role();
        $roles = $roleModel->findAll();

        $this->render('users/create', ['roles' => $roles]);
    }

    public function store() {
        $this->requireLogin();
        $this->requireRole('admin');

        $data = [
            'username' => $_POST['username'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT),
            'role_id' => $_POST['role_id'] ?? '',
            'first_name' => $_POST['first_name'] ?? '',
            'last_name' => $_POST['last_name'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];

        $userModel = new User();
        $userId = $userModel->create($data);

        if ($userId) {
            $_SESSION['success'] = 'User created successfully';
        } else {
            $_SESSION['error'] = 'Failed to create user';
        }

        $this->redirect('/users');
    }

    public function show($id = null) {
        $this->requireLogin();
        $id = $id ?? $_GET['id'] ?? 0;

        $userModel = new User();
        $user = $userModel->getWithRole($id);

        if (!$user) {
            $this->redirect('/users');
        }

        $this->render('users/show', ['user' => $user]);
    }

    public function edit() {
        $this->requireLogin();
        $this->requireRole('admin');

        $id = $_GET['id'] ?? 0;
        $userModel = new User();
        $user = $userModel->getWithRole($id);

        if (!$user) {
            $this->redirect('/users');
        }

        $roleModel = new Role();
        $roles = $roleModel->findAll();

        $this->render('users/edit', ['user' => $user, 'roles' => $roles]);
    }

    public function update() {
        $this->requireLogin();
        $this->requireRole('admin');

        $id = $_POST['id'] ?? 0;
        $data = [
            'username' => $_POST['username'] ?? '',
            'email' => $_POST['email'] ?? '',
            'role_id' => $_POST['role_id'] ?? '',
            'first_name' => $_POST['first_name'] ?? '',
            'last_name' => $_POST['last_name'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];

        // Only update password if provided
        if (!empty($_POST['password'])) {
            $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }

        $userModel = new User();
        $result = $userModel->update($id, $data);

        if ($result) {
            $_SESSION['success'] = 'User updated successfully';
        } else {
            $_SESSION['error'] = 'Failed to update user';
        }

        $this->redirect('/users');
    }

    public function delete() {
        $this->requireLogin();
        $this->requireRole('admin');

        $id = $_POST['id'] ?? 0;

        $userModel = new User();
        $result = $userModel->delete($id);

        if ($result) {
            $_SESSION['success'] = 'User deleted successfully';
        } else {
            $_SESSION['error'] = 'Failed to delete user';
        }

        $this->redirect('/users');
    }
}
?>