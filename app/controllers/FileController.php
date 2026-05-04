<?php
class FileController extends Controller
{
    public function index(): void
    {
        $this->authorize(['therapist', 'patient', 'admin']);
        $uploads = (new FileUpload())->listByUser($this->auth->user()['id']);
        $this->view->render('files/index', ['uploads' => $uploads]);
    }

    public function upload(): void
    {
        $this->authorize(['therapist', 'patient']);
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['document'])) {
            $service = new DocumentService();
            if (!$service->validateUpload($_FILES['document'])) {
                $error = 'Upload failed. Allowed formats: pdf, doc, docx, jpg, png and max 5MB.';
            } else {
                $filename = $service->secureName($_FILES['document']['name']);
                $target = __DIR__ . '/../../uploads/' . $filename;
                move_uploaded_file($_FILES['document']['tmp_name'], $target);
                (new FileUpload())->saveRecord([
                    'user_id' => $this->auth->user()['id'],
                    'filename' => $filename,
                    'original_name' => $_FILES['document']['name'],
                    'file_type' => $_FILES['document']['type'],
                ]);
                AuditLog::record($this->auth->user()['id'], 'file.upload', 'Uploaded a secure document.');
                $this->redirect($this->config['app']['base_url'] . '?controller=file&action=index');
            }
        }
        $this->view->render('files/form', ['error' => $error]);
    }
}
