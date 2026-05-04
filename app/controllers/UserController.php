<?php
class UserController extends Controller
{
    public function index(): void
    {
        $this->authorize(['admin']);
        $userModel = new User();
        $query = trim($_GET['q'] ?? '');
        $users = $query ? $userModel->search($query) : $userModel->all();
        $this->view->render('users/list', ['users' => $users, 'query' => $query]);
    }

    public function add(): void
    {
        $this->authorize(['admin']);
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            if ($userModel->findByEmail(trim($_POST['email'] ?? ''))) {
                $error = 'Email is already in use';
            } else {
                $userId = 0;
                try {
                    $userId = $userModel->create([
                        'name' => trim($_POST['name'] ?? ''),
                        'email' => trim($_POST['email'] ?? ''),
                        'password' => $_POST['password'] ?? '',
                        'role' => $_POST['role'] ?? 'patient',
                    ]);
                    if ($_POST['role'] === 'patient') {
                        (new Patient())->createProfile($userId, ['timezone' => 'UTC', 'preferences' => '', 'intake_status' => 'pending']);
                    }
                    if ($_POST['role'] === 'therapist') {
                        (new Therapist())->createProfile($userId, [
                            'specialization' => trim($_POST['specialization'] ?? 'General'),
                            'license_number' => trim($_POST['license_number'] ?? ''),
                            'availability' => trim($_POST['availability'] ?? 'weekdays'),
                            'rating' => (int)($_POST['rating'] ?? 0),
                        ]);
                    }
                    AuditLog::record($this->auth->user()['id'], 'user.add', 'Created new user.');
                    $this->redirect($this->config['app']['base_url'] . '?controller=user&action=index');
                    return;
                } catch (Exception $ex) {
                    if ($userId > 0) {
                        (new User())->delete($userId);
                    }
                    $error = 'Unable to add user. Please try again later.';
                }
            }
        }
        $this->view->render('users/form', ['error' => $error, 'action' => 'add']);
    }

    public function edit(): void
    {
        $this->authorize(['admin']);
        $id = (int)($_GET['id'] ?? 0);
        $userModel = new User();
        $user = $userModel->findById($id);
        if (!$user) {
            $this->view->render('errors/404');
            return;
        }
        if ($user['role'] === 'therapist') {
            $profile = (new Therapist())->profile($id);
            if ($profile) {
                $user = array_merge($user, $profile);
            }
        }
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel->update($id, [
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'role' => $_POST['role'] ?? $user['role'],
                'status' => $_POST['status'] ?? $user['status'],
            ]);
            if (($_POST['role'] ?? $user['role']) === 'therapist') {
                $therapist = new Therapist();
                if ($therapist->profile($id)) {
                    $therapist->updateProfile($id, [
                        'specialization' => trim($_POST['specialization'] ?? 'General'),
                        'license_number' => trim($_POST['license_number'] ?? ''),
                        'availability' => trim($_POST['availability'] ?? 'weekdays'),
                        'rating' => (int)($_POST['rating'] ?? 0),
                    ]);
                } else {
                    $therapist->createProfile($id, [
                        'specialization' => trim($_POST['specialization'] ?? 'General'),
                        'license_number' => trim($_POST['license_number'] ?? ''),
                        'availability' => trim($_POST['availability'] ?? 'weekdays'),
                        'rating' => (int)($_POST['rating'] ?? 0),
                    ]);
                }
            }
            AuditLog::record($this->auth->user()['id'], 'user.edit', 'Updated user profile.');
            $this->redirect($this->config['app']['base_url'] . '?controller=user&action=index');
        }
        $this->view->render('users/form', ['user' => $user, 'error' => $error, 'action' => 'edit']);
    }

    public function delete(): void
    {
        $this->authorize(['admin']);
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            (new User())->delete($id);
            AuditLog::record($this->auth->user()['id'], 'user.delete', 'Deleted a user.');
        }
        $this->redirect($this->config['app']['base_url'] . '?controller=user&action=index');
    }
}
