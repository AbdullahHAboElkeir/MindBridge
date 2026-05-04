<?php
class AppointmentController extends Controller
{
    public function index(): void
    {
        $this->authorize(['admin', 'therapist', 'patient']);
        $appointments = (new Appointment())->all();
        $this->view->render('appointments/index', ['appointments' => $appointments]);
    }

    public function book(): void
    {
        $this->authorize(['patient']);
        $criteria = [
            'specialization' => trim($_GET['specialization'] ?? ''),
            'timezone' => trim($_GET['timezone'] ?? ''),
            'license_number' => trim($_GET['license_number'] ?? ''),
        ];

        $therapists = (new MatchingEngine())->matchTherapists($criteria);
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $start = trim($_POST['start_time'] ?? '');
                $end = trim($_POST['end_time'] ?? '');
                (new Appointment())->book([
                    'patient_id' => $this->auth->user()['id'],
                    'therapist_id' => (int)($_POST['therapist_id'] ?? 0),
                    'start_time' => $start,
                    'end_time' => $end,
                    'timezone' => trim($_POST['timezone'] ?? 'UTC'),
                    'notes' => trim($_POST['notes'] ?? ''),
                ]);
                AuditLog::record($this->auth->user()['id'], 'appointment.book', 'Booked a new session.');
                $this->redirect($this->config['app']['base_url'] . '?controller=dashboard&action=index');
            } catch (Exception $ex) {
                $error = $ex->getMessage();
            }
        }
        $this->view->render('appointments/form', ['therapists' => $therapists, 'criteria' => $criteria, 'error' => $error]);
    }
}
