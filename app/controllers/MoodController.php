<?php
class MoodController extends Controller
{
    public function index(): void
    {
        $this->authorize(['patient']);
        $entries = (new MoodTracker())->recent($this->auth->user()['id']);
        $this->view->render('mood/index', ['entries' => $entries]);
    }

    public function add(): void
    {
        $this->authorize(['patient']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->isAjax()) {
            $mood = new MoodTracker();
            $mood->addEntry([
                'patient_id' => $this->auth->user()['id'],
                'mood_level' => (int)($_POST['mood_level'] ?? 3),
                'note' => trim($_POST['note'] ?? ''),
                'mood_date' => $_POST['mood_date'] ?? date('Y-m-d'),
            ]);
            echo json_encode(['status' => 'success']);
            return;
        }
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    }
}
