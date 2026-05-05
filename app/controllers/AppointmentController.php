<?php

class AppointmentController extends Controller
{
    private Appointment $apptModel;

    public function __construct()
    {
        parent::__construct();
        Middleware::requireAuth();
        $this->apptModel = $this->model('Appointment');
    }

    /** GET /appointments */
    public function index(): void
    {
        $role = Session::role();
        $userId = Session::userId();

        if ($role === 'patient') {
            $patient = $this->db->fetchOne("SELECT id FROM patients WHERE user_id=?", [$userId]);
            $appointments = $this->apptModel->getForPatient($patient['id'] ?? 0);
        } elseif ($role === 'therapist') {
            $therapist = $this->db->fetchOne("SELECT id FROM therapists WHERE user_id=?", [$userId]);
            $appointments = $this->apptModel->getForTherapist($therapist['id'] ?? 0);
        } else {
            // Admin sees all
            $appointments = $this->db->fetchAll(
                "SELECT a.*, pu.first_name AS p_first, pu.last_name AS p_last,
                        tu.first_name AS t_first, tu.last_name AS t_last
                 FROM appointments a
                 JOIN patients p ON p.id=a.patient_id JOIN users pu ON pu.id=p.user_id
                 JOIN therapists t ON t.id=a.therapist_id JOIN users tu ON tu.id=t.user_id
                 ORDER BY a.scheduled_at DESC LIMIT 50");
        }

        $pageTitle = 'Appointments';
        $this->view('appointments.index', compact('pageTitle','appointments','role'));
    }

    /** GET /appointments/book */
    public function book(): void
    {
        Middleware::requirePatient();
        $patient = $this->db->fetchOne("SELECT p.*, u.first_name, u.last_name FROM patients p JOIN users u ON u.id=p.user_id WHERE p.user_id=?", [Session::userId()]);
        $therapists = $this->db->fetchAll(
            "SELECT t.id, t.session_rate, t.languages, t.specializations, u.first_name, u.last_name, u.gender
             FROM therapists t JOIN users u ON u.id=t.user_id WHERE u.status='active' AND t.is_available=1 ORDER BY t.rating DESC"
        );
        
        // Determine default therapist ID
        $defaultTherapistId = 0;
        if ($patient && !empty($patient['assigned_therapist'])) {
            $therapist = $this->db->fetchOne("SELECT id FROM therapists WHERE user_id=?", [$patient['assigned_therapist']]);
            $defaultTherapistId = $therapist['id'] ?? 0;
        }
        $selectedTherapistId = (int)$this->get('therapist_id', $defaultTherapistId);
        
        $pageTitle = 'Book a Session';
        $this->view('appointments.book', compact('pageTitle','patient','therapists','selectedTherapistId'));
    }

    /** POST /appointments/store */
    public function store(): void
    {
        Middleware::requirePatient();
        if (!$this->isPost()) { $this->redirect('appointments/book'); }

        $patient = $this->db->fetchOne("SELECT id FROM patients WHERE user_id=?", [Session::userId()]);
        if (!$patient) { 
            Session::flash('error', 'Patient record not found.');
            $this->redirect('appointments/book'); 
        }
        
        $therapistId = (int)$this->post('therapist_id');
        $date        = $this->post('date');
        $time        = $this->post('time');
        $type        = $this->post('type', 'video');
        $notes       = $this->post('notes', '');
        $duration    = 50;

        if (!$therapistId || !$date || !$time) {
            Session::flash('error', 'Please fill all required fields.');
            $this->redirect('appointments/book');
        }

        $datetime = $date . ' ' . $time . ':00';
        if (strtotime($datetime) < time()) {
            Session::flash('error', 'Please select a future date and time.');
            $this->redirect('appointments/book');
        }

        if ($this->apptModel->hasConflict($therapistId, $datetime, $duration)) {
            Session::flash('error', 'That time slot is already booked. Please choose another.');
            $this->redirect('appointments/book');
        }

        $apptId = $this->apptModel->book($patient['id'], $therapistId, $datetime, $duration, $type, $notes);

        // Notify therapist
        $therapist = $this->db->fetchOne("SELECT user_id FROM therapists WHERE id=?", [$therapistId]);
        if ($therapist) {
            $this->db->insert(
                "INSERT INTO notifications (user_id, type, title, message, link, created_at)
                 VALUES (?,'new_appointment','New Session Booked',?,'/appointments',NOW())",
                [$therapist['user_id'], Session::get('first_name').' '.Session::get('last_name').' booked a session on '.date('M j, Y g:i A', strtotime($datetime)).'.']
            );
        }

        $this->auditLog('book_appointment','appointments',"Booked appointment ID: $apptId");
        Session::flash('success', 'Session booked for '.date('D, M j, Y \a\t g:i A', strtotime($datetime)).'.');
        $this->redirect('appointments');
    }

    /** POST /appointments/cancel/{id} */
    public function cancel(int $id): void
    {
        $appt = $this->apptModel->getById($id);
        if (!$appt) { Session::flash('error','Appointment not found.'); $this->redirect('appointments'); }

        $reason = $this->post('cancel_reason','');
        $this->apptModel->cancel($id, Session::userId(), $reason);
        $this->auditLog('cancel_appointment','appointments',"Cancelled ID: $id");
        Session::flash('success','Appointment cancelled.');
        $this->redirect('appointments');
    }

    /** GET /appointments/reschedule/{id} */
    public function reschedule(int $id): void
    {
        $appt = $this->apptModel->getById($id);
        if (!$appt) { $this->redirect('appointments'); }
        $pageTitle = 'Reschedule Appointment';
        $this->view('appointments.reschedule', compact('pageTitle','appt'));
    }

    /** POST /appointments/doReschedule */
    public function doReschedule(): void
    {
        $id       = (int)$this->post('appointment_id');
        $date     = $this->post('date');
        $time     = $this->post('time');
        $appt     = $this->apptModel->getById($id);
        $datetime = $date . ' ' . $time . ':00';

        if (!$appt || strtotime($datetime) < time()) {
            Session::flash('error','Invalid date/time.'); $this->redirect('appointments');
        }
        if ($this->apptModel->hasConflict($appt['therapist_id'], $datetime, 50, $id)) {
            Session::flash('error','That slot is already taken.'); $this->redirect("appointments/reschedule/$id");
        }
        $this->apptModel->reschedule($id, $datetime);
        $this->auditLog('reschedule','appointments',"Rescheduled ID: $id to $datetime");
        Session::flash('success','Appointment rescheduled.');
        $this->redirect('appointments');
    }

    /** GET /appointments/waitingRoom/{id} */
    public function waitingRoom(int $id): void
    {
        $appt = $this->apptModel->getById($id);
        if (!$appt) { $this->redirect('appointments'); }
        $pageTitle = 'Virtual Waiting Room';
        $this->view('appointments.waiting_room', compact('pageTitle','appt'));
    }

    /** GET /appointments/slots — AJAX */
    public function slots(): void
    {
        $therapistId = (int)$this->get('therapist_id');
        $date        = $this->get('date');
        if (!$therapistId || !$date) { $this->json(['slots' => []]); }
        $slots = $this->apptModel->getAvailableSlots($therapistId, $date);
        $this->json(['slots' => $slots]);
    }
}
