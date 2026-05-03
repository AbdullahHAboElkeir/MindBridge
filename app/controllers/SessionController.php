<?php
// app/controllers/SessionController.php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/Session.php';
require_once __DIR__ . '/../models/Therapist.php';
require_once __DIR__ . '/../models/Patient.php';

class SessionController extends Controller {
    public function index() {
        $this->requireLogin();
        $user = $this->getCurrentUser();

        $sessionModel = new Session();
        $sessions = $sessionModel->getByUser($user['id'], $user['role_name']);

        $this->render('sessions/index', ['sessions' => $sessions]);
    }

    public function create() {
        $this->requireLogin();
        $user = $this->getCurrentUser();

        if ($user['role_name'] === 'patient') {
            $therapistModel = new Therapist();
            $therapists = $therapistModel->findAll();
            $this->render('sessions/create', ['therapists' => $therapists]);
        } else {
            $this->redirect('/sessions');
        }
    }

    public function store() {
        $this->requireLogin();
        $user = $this->getCurrentUser();

        if ($user['role_name'] !== 'patient') {
            $this->redirect('/sessions');
        }

        $data = [
            'patient_id' => $user['id'],
            'therapist_id' => $_POST['therapist_id'] ?? '',
            'scheduled_date' => $_POST['scheduled_date'] ?? '',
            'duration_minutes' => $_POST['duration_minutes'] ?? 60,
            'session_type' => $_POST['session_type'] ?? 'individual',
            'notes' => $_POST['notes'] ?? ''
        ];

        $sessionModel = new Session();
        $sessionId = $sessionModel->create($data);

        if ($sessionId) {
            $_SESSION['success'] = 'Session booked successfully';
        } else {
            $_SESSION['error'] = 'Failed to book session';
        }

        $this->redirect('/sessions');
    }

    public function show() {
        $this->requireLogin();
        $id = $_GET['id'] ?? 0;

        $sessionModel = new Session();
        $session = $sessionModel->getWithDetails($id);

        if (!$session) {
            $this->redirect('/sessions');
        }

        $this->render('sessions/show', ['session' => $session]);
    }
}
?>