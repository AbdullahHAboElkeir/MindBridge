<?php

class TherapistController extends Controller
{
    private Therapist $therapistModel;

    public function __construct()
    {
        parent::__construct();
        Middleware::requireTherapist();
        $this->therapistModel = $this->model('Therapist');
    }

    /** GET /therapist/profile */
    public function profile(): void
    {
        $therapist = $this->therapistModel->getByUserId(Session::userId());
        $pageTitle = 'My Profile';
        $this->view('therapist.profile', compact('pageTitle','therapist'));
    }

    /** POST /therapist/updateProfile */
    public function updateProfile(): void
    {
        if (!$this->isPost()) { $this->redirect('therapist/profile'); }
        $therapist = $this->therapistModel->getByUserId(Session::userId());
        $data = [
            'first_name'       => $this->post('first_name'),
            'last_name'        => $this->post('last_name'),
            'phone'            => $this->post('phone'),
            'specializations'  => $this->post('specializations'),
            'languages'        => $this->post('languages'),
            'bio'              => $this->post('bio'),
            'years_experience' => $this->post('years_experience', 0),
            'session_rate'     => $this->post('session_rate', 0),
            'accepts_insurance'=> $this->post('accepts_insurance'),
        ];
        if (!empty($_FILES['avatar']['name'])) {
            $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','webp']) && $_FILES['avatar']['size'] <= MAX_FILE_SIZE) {
                $fn = 'avatar_' . Session::userId() . '_' . time() . '.' . $ext;
                move_uploaded_file($_FILES['avatar']['tmp_name'], UPLOAD_PATH . $fn);
                $this->db->execute("UPDATE users SET avatar=? WHERE id=?", [$fn, Session::userId()]);
            }
        }
        $this->therapistModel->updateProfile($therapist['id'], Session::userId(), $data);
        Session::set('first_name', $data['first_name']);
        Session::flash('success', 'Profile updated.');
        $this->redirect('therapist/profile');
    }

    /** GET /therapist/availability */
    public function availability(): void
    {
        $therapist    = $this->therapistModel->getByUserId(Session::userId());
        $availability = $this->therapistModel->getAvailability($therapist['id']);
        $pageTitle    = 'Manage Availability';
        $this->view('therapist.availability', compact('pageTitle','therapist','availability'));
    }

    /** POST /therapist/updateAvailability */
    public function updateAvailability(): void
    {
        if (!$this->isPost()) { $this->redirect('therapist/availability'); }
        $therapist = $this->therapistModel->getByUserId(Session::userId());
        $slots = $_POST['slots'] ?? [];
        $this->therapistModel->setAvailability($therapist['id'], $slots);
        $this->auditLog('update_availability','therapist_availability','Availability updated');
        Session::flash('success', 'Availability updated successfully.');
        $this->redirect('therapist/availability');
    }

    /** GET /therapist/patients */
    public function patients(): void
    {
        $therapist = $this->therapistModel->getByUserId(Session::userId());
        $patients  = $this->therapistModel->getPatients($therapist['id']);
        $pageTitle = 'My Patients';
        $this->view('therapist.patients', compact('pageTitle','therapist','patients'));
    }

    /** GET /therapist/sessionNotes/{apptId} */
    public function sessionNotes(int $apptId): void
    {
        require_once BASE_PATH . '/app/models/Appointment.php';
        $sessionModel = new SessionRecord();
        $appt   = (new Appointment())->getById($apptId);
        if (!$appt) { $this->redirect('appointments'); }
        $notes  = $sessionModel->getByAppointmentId($apptId);
        $pageTitle = 'Session Notes';
        $this->view('therapist.session_notes', compact('pageTitle','appt','notes'));
    }

    /** POST /therapist/saveNotes */
    public function saveNotes(): void
    {
        if (!$this->isPost()) { $this->redirect('appointments'); }
        require_once BASE_PATH . '/app/models/Appointment.php';
        $apptId = (int)$this->post('appointment_id');
        $data   = [
            'therapist_notes' => $this->post('therapist_notes'),
            'techniques_used' => $this->post('techniques_used'),
            'homework'        => $this->post('homework'),
            'outcome'         => $this->post('outcome'),
            'follow_up_date'  => $this->post('follow_up_date'),
        ];
        (new SessionRecord())->saveNotes($apptId, $data);
        // Mark appointment completed
        (new Appointment())->updateStatus($apptId, 'completed');
        $this->auditLog('save_session_notes','sessions',"Notes for appointment ID: $apptId");
        Session::flash('success', 'Session notes saved.');
        $this->redirect('appointments');
    }
}
