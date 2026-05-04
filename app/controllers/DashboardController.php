<?php
class DashboardController extends Controller
{
    public function index(): void
    {
        $this->authorize(['patient', 'therapist', 'admin']);
        $user = $this->auth->user();
        if ($user['role'] === 'admin') {
            $admin = new Admin();
            $metrics = $admin->dashboardMetrics();
            $this->view->render('dashboard/admin', ['user' => $user, 'metrics' => $metrics]);
            return;
        }
        if ($user['role'] === 'therapist') {
            $appointments = (new Appointment())->findByTherapist($user['id']);
            $this->view->render('dashboard/therapist', ['user' => $user, 'appointments' => $appointments]);
            return;
        }
        if ($user['role'] === 'patient') {
            $moodEntries = (new MoodTracker())->recent($user['id']);
            $appointments = (new Appointment())->findByPatient($user['id']);
            $this->view->render('dashboard/patient', ['user' => $user, 'moodEntries' => $moodEntries, 'appointments' => $appointments]);
            return;
        }
        $this->view->render('dashboard/patient', ['user' => $user]);
    }
}
