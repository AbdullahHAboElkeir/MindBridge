<?php
// app/controllers/ReportController.php

require_once __DIR__ . '/../../core/Controller.php';

class ReportController extends Controller {
    public function index() {
        $this->requireLogin();
        $user = $this->getCurrentUser();

        // Basic reports view
        $this->render('reports/index', ['user' => $user]);
    }

    public function generate() {
        $this->requireLogin();
        $user = $this->getCurrentUser();

        $type = $_GET['type'] ?? 'session';

        // Simple report generation
        $reportData = [];

        switch ($type) {
            case 'mood':
                if ($user['role_name'] === 'patient') {
                    require_once __DIR__ . '/../models/MoodTracker.php';
                    $moodModel = new MoodTracker();
                    $reportData = $moodModel->getByPatient($user['id']);
                }
                break;
            case 'session':
                require_once __DIR__ . '/../models/Session.php';
                $sessionModel = new Session();
                $reportData = $sessionModel->getByUser($user['id'], $user['role_name']);
                break;
        }

        $this->render('reports/generate', [
            'type' => $type,
            'data' => $reportData,
            'user' => $user
        ]);
    }
}
?>