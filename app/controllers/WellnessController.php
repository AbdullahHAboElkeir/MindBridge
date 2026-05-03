<?php
// app/controllers/WellnessController.php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/MoodTracker.php';
require_once __DIR__ . '/../models/Journal.php';

class WellnessController extends Controller {
    public function index() {
        $this->requireLogin();
        $user = $this->getCurrentUser();

        if ($user['role_name'] !== 'patient') {
            $this->redirect('/dashboard');
        }

        $moodModel = new MoodTracker();
        $journalModel = new Journal();

        $moods = $moodModel->getByPatient($user['id']);
        $journals = $journalModel->getByPatient($user['id']);
        $averageMood = $moodModel->getAverageMood($user['id']);

        $this->render('wellness/index', [
            'moods' => $moods,
            'journals' => $journals,
            'average_mood' => $averageMood
        ]);
    }

    public function storeMood() {
        $this->requireLogin();
        $user = $this->getCurrentUser();

        if ($user['role_name'] !== 'patient') {
            $this->redirect('/dashboard');
        }

        $data = [
            'patient_id' => $user['id'],
            'mood_level' => $_POST['mood_level'] ?? 5,
            'notes' => $_POST['notes'] ?? ''
        ];

        $moodModel = new MoodTracker();
        $moodModel->create($data);

        // AJAX response
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            echo json_encode(['success' => true]);
            exit;
        }

        $_SESSION['success'] = 'Mood recorded successfully';
        $this->redirect('/wellness');
    }

    public function journals() {
        $this->requireLogin();
        $user = $this->getCurrentUser();

        if ($user['role_name'] !== 'patient') {
            $this->redirect('/dashboard');
        }

        $journalModel = new Journal();
        $journals = $journalModel->getByPatient($user['id']);

        $this->render('wellness/journals', ['journals' => $journals]);
    }

    public function storeJournal() {
        $this->requireLogin();
        $user = $this->getCurrentUser();

        if ($user['role_name'] !== 'patient') {
            $this->redirect('/dashboard');
        }

        $data = [
            'patient_id' => $user['id'],
            'title' => $_POST['title'] ?? '',
            'content' => $_POST['content'] ?? '',
            'is_private' => isset($_POST['is_private']) ? 1 : 0
        ];

        $journalModel = new Journal();
        $journalModel->create($data);

        $_SESSION['success'] = 'Journal entry saved successfully';
        $this->redirect('/journals');
    }
}
?>