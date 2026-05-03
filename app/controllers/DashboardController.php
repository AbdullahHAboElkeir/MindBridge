<?php
// app/controllers/DashboardController.php

require_once __DIR__ . '/../../core/Controller.php';

class DashboardController extends Controller {
    public function index() {
        $this->requireLogin();
        $user = $this->getCurrentUser();

        $data = ['user' => $user];

        // Role-specific data
        switch ($user['role_name']) {
            case 'patient':
                $data = array_merge($data, $this->getPatientDashboard($user['id']));
                break;
            case 'therapist':
                $data = array_merge($data, $this->getTherapistDashboard($user['id']));
                break;
            case 'admin':
                $data = array_merge($data, $this->getAdminDashboard());
                break;
            case 'clinic_manager':
                $data = array_merge($data, $this->getManagerDashboard($user['id']));
                break;
        }

        $this->render('dashboard/' . $user['role_name'], $data);
    }

    private function getPatientDashboard($userId) {
        require_once __DIR__ . '/../models/Session.php';
        require_once __DIR__ . '/../models/MoodTracker.php';

        $sessionModel = new Session();
        $moodModel = new MoodTracker();

        return [
            'upcoming_sessions' => $sessionModel->getByUser($userId, 'patient'),
            'recent_moods' => $moodModel->getByPatient($userId),
            'average_mood' => $moodModel->getAverageMood($userId)
        ];
    }

    private function getTherapistDashboard($userId) {
        require_once __DIR__ . '/../models/Session.php';

        $sessionModel = new Session();

        return [
            'today_sessions' => $sessionModel->getByUser($userId, 'therapist'),
            'total_patients' => 0 // Would need to count distinct patients
        ];
    }

    private function getAdminDashboard() {
        require_once __DIR__ . '/../models/User.php';

        $userModel = new User();

        return [
            'total_users' => count($userModel->findAll()),
            'total_patients' => count($userModel->getByRole('patient')),
            'total_therapists' => count($userModel->getByRole('therapist'))
        ];
    }

    private function getManagerDashboard($userId) {
        // Similar to admin but scoped to clinic
        return $this->getAdminDashboard();
    }
}
?>