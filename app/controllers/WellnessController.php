<?php

class WellnessController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Middleware::requireAuth();
    }

    private function getPatient(): array
    {
        $patient = $this->db->fetchOne("SELECT * FROM patients WHERE user_id=?", [Session::userId()]);
        if (!$patient) { $this->redirect('dashboard'); }
        return $patient;
    }

    /* ── Mood Tracker ───────────────────────────────────────── */
    public function mood(): void
    {
        Middleware::requirePatient();
        require_once BASE_PATH . '/app/models/Wellness.php';
        $patient    = $this->getPatient();
        $moodModel  = new MoodEntry();
        $todayMood  = $moodModel->getToday($patient['id']);
        $last30     = $moodModel->getLast30Days($patient['id']);
        $avg7       = $moodModel->getAverage($patient['id'], 7);
        $avg30      = $moodModel->getAverage($patient['id'], 30);
        $moodLabels = array_column($last30, 'entry_date');
        $moodValues = array_column($last30, 'mood_level');
        $pageTitle  = 'Mood Tracker';
        $this->view('wellness.mood', compact('pageTitle','patient','todayMood','last30','avg7','avg30','moodLabels','moodValues'));
    }

    /** POST /wellness/storeMood — AJAX + normal */
    public function storeMood(): void
    {
        Middleware::requirePatient();
        require_once BASE_PATH . '/app/models/Wellness.php';
        $patient   = $this->getPatient();
        $moodModel = new MoodEntry();
        $level     = max(1, min(10, (int)$this->post('mood_level', 5)));
        $notes     = $this->post('notes', '');
        $triggers  = $this->post('triggers', '');
        $activities= $this->post('activities', '');

        // Crisis check
        $crisisWords = CRISIS_KEYWORDS;
        $text = strtolower($notes . ' ' . $triggers);
        $isCrisis = false;
        foreach ($crisisWords as $kw) {
            if (str_contains($text, $kw)) { $isCrisis = true; break; }
        }
        if ($isCrisis) {
            $this->db->insert(
                "INSERT INTO crisis_alerts (patient_id, trigger_text, source, severity, status, created_at)
                 VALUES (?, 'mood entry', 'mood', ?, 'new', NOW())",
                [$patient['id'], $level <= 3 ? 'high' : 'medium']
            );
        }

        $result = $moodModel->save($patient['id'], $level, $notes, $triggers, $activities);

        if ($this->isAjax()) {
            $this->json(['success' => true, 'mood' => $level, 'message' => 'Mood logged!']);
        }
        Session::flash('success', 'Mood logged for today!');
        $this->redirect('wellness/mood');
    }

    /* ── Journal ────────────────────────────────────────────── */
    public function journal(): void
    {
        Middleware::requirePatient();
        require_once BASE_PATH . '/app/models/Wellness.php';
        $patient      = $this->getPatient();
        $journalModel = new Journal();
        $page         = (int)$this->get('page', 1);
        $entries      = $journalModel->getAll($patient['id'], $page);
        $total        = $journalModel->count($patient['id']);
        $pages        = ceil($total / ITEMS_PER_PAGE);
        $editEntry    = null;
        $editId       = (int)$this->get('edit', 0);
        if ($editId) $editEntry = $journalModel->getById($editId, $patient['id']);
        $pageTitle = 'My Journal';
        $this->view('wellness.journal', compact('pageTitle','patient','entries','total','pages','page','editEntry'));
    }

    /** POST /wellness/storeJournal */
    public function storeJournal(): void
    {
        Middleware::requirePatient();
        require_once BASE_PATH . '/app/models/Wellness.php';
        $patient      = $this->getPatient();
        $journalModel = new Journal();
        $id           = (int)$this->post('entry_id', 0);
        $data         = [
            'title'    => $this->post('title'),
            'content'  => $this->post('content'),
            'mood_tag' => $this->post('mood_tag'),
        ];
        if (empty($data['title']) || empty($data['content'])) {
            Session::flash('error', 'Title and content are required.');
            $this->redirect('wellness/journal');
        }

        // Crisis check
        $text = strtolower($data['content']);
        foreach (CRISIS_KEYWORDS as $kw) {
            if (str_contains($text, $kw)) {
                $this->db->insert(
                    "INSERT INTO crisis_alerts (patient_id, trigger_text, source, severity, status, created_at)
                     VALUES (?,?,'journal','high','new',NOW())",
                    [$patient['id'], substr($data['content'], 0, 500)]);
                $this->auditLog('crisis_detected','journals','Crisis keywords in journal');
                break;
            }
        }

        $journalModel->save($patient['id'], $data, $id);
        Session::flash('success', $id ? 'Entry updated.' : 'Journal entry saved.');
        $this->redirect('wellness/journal');
    }

    /** GET /wellness/deleteJournal/{id} */
    public function deleteJournal(int $id): void
    {
        Middleware::requirePatient();
        require_once BASE_PATH . '/app/models/Wellness.php';
        $patient = $this->getPatient();
        (new Journal())->delete($id, $patient['id']);
        Session::flash('success', 'Entry deleted.');
        $this->redirect('wellness/journal');
    }

    /* ── Goals ──────────────────────────────────────────────── */
    public function goals(): void
    {
        Middleware::requirePatient();
        require_once BASE_PATH . '/app/models/Wellness.php';
        $patient   = $this->getPatient();
        $goalModel = new WellnessGoal();
        $goals     = $goalModel->getAll($patient['id']);
        $pageTitle = 'My Wellness Goals';
        $this->view('wellness.goals', compact('pageTitle','patient','goals'));
    }

    /** POST /wellness/storeGoal */
    public function storeGoal(): void
    {
        Middleware::requirePatient();
        require_once BASE_PATH . '/app/models/Wellness.php';
        $patient = $this->getPatient();
        $id      = (int)$this->post('goal_id', 0);
        $data    = [
            'title'       => $this->post('title'),
            'description' => $this->post('description'),
            'category'    => $this->post('category', 'mental'),
            'target_date' => $this->post('target_date'),
            'status'      => 'active',
        ];
        (new WellnessGoal())->save($patient['id'], $data, $id);
        Session::flash('success', $id ? 'Goal updated.' : 'Goal created!');
        $this->redirect('wellness/goals');
    }

    /** POST /wellness/updateGoalProgress — AJAX */
    public function updateGoalProgress(): void
    {
        Middleware::requirePatient();
        require_once BASE_PATH . '/app/models/Wellness.php';
        $patient  = $this->getPatient();
        $goalId   = (int)$this->post('goal_id');
        $progress = max(0, min(100, (int)$this->post('progress', 0)));
        (new WellnessGoal())->updateProgress($goalId, $patient['id'], $progress);
        if ($this->isAjax()) { $this->json(['success' => true, 'progress' => $progress]); }
        $this->redirect('wellness/goals');
    }

    /** GET /wellness/deleteGoal/{id} */
    public function deleteGoal(int $id): void
    {
        Middleware::requirePatient();
        require_once BASE_PATH . '/app/models/Wellness.php';
        $patient = $this->getPatient();
        (new WellnessGoal())->delete($id, $patient['id']);
        Session::flash('success', 'Goal removed.');
        $this->redirect('wellness/goals');
    }

    /* ── Resources ──────────────────────────────────────────── */
    public function resources(): void
    {
        require_once BASE_PATH . '/app/models/Wellness.php';
        $category  = $this->get('category', '');
        $type      = $this->get('type', '');
        $model     = new WellnessResource();
        $resources = $model->getAll($category, $type);
        $featured  = $model->getFeatured(3);
        $pageTitle = 'Wellness Resources';
        $this->view('wellness.resources', compact('pageTitle','resources','featured','category','type'));
    }

    /** GET /wellness/resource/{id} */
    public function resource(int $id): void
    {
        require_once BASE_PATH . '/app/models/Wellness.php';
        $resource  = (new WellnessResource())->getById($id);
        if (!$resource) { $this->redirect('wellness/resources'); }
        $pageTitle = $resource['title'];
        $this->view('wellness.resource_view', compact('pageTitle','resource'));
    }
}
