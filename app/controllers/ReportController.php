<?php
class ReportController extends Controller
{
    public function index(): void
    {
        $this->authorize(['admin', 'therapist']);
        $reports = (new Report())->all();
        $this->view->render('reports/index', ['reports' => $reports]);
    }

    public function generate(): void
    {
        $this->authorize(['admin', 'therapist']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new Report())->create([
                'user_id' => $this->auth->user()['id'],
                'title' => trim($_POST['title'] ?? 'Therapist Analytics'),
                'content' => trim($_POST['content'] ?? ''),
                'category' => trim($_POST['category'] ?? 'analytics'),
            ]);
            AuditLog::record($this->auth->user()['id'], 'report.generate', 'Generated a new report.');
            $this->redirect($this->config['app']['base_url'] . '?controller=report&action=index');
        }
        $this->view->render('reports/form', []);
    }
}
