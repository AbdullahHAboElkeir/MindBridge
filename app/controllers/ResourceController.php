<?php
class ResourceController extends Controller
{
    public function index(): void
    {
        $this->authorize(['admin', 'therapist', 'patient']);
        $resources = (new Resource())->all();
        $this->view->render('resources/index', ['resources' => $resources]);
    }

    public function add(): void
    {
        $this->authorize(['admin', 'therapist']);
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (trim($_POST['title'] ?? '') === '') {
                $error = 'Please provide a title.';
            } else {
                (new Resource())->create([
                    'title' => trim($_POST['title']),
                    'description' => trim($_POST['description'] ?? ''),
                    'type' => trim($_POST['type'] ?? 'guide'),
                    'file_path' => trim($_POST['file_path'] ?? ''),
                    'created_by' => $this->auth->user()['id'],
                ]);
                AuditLog::record($this->auth->user()['id'], 'resource.add', 'Added resource content.');
                $this->redirect($this->config['app']['base_url'] . '?controller=resource&action=index');
            }
        }
        $this->view->render('resources/form', ['error' => $error]);
    }
}
