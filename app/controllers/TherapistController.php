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

    /** GET /therapist/schedule — alias for availability() */
    public function schedule(): void
    {
        $this->availability();
    }

    /** GET /therapist/patients */
    public function patients(): void
    {
        $therapist = $this->therapistModel->getByUserId(Session::userId());
        $patients  = $this->therapistModel->getPatients($therapist['id']);
        $pageTitle = 'My Patients';
        $this->view('therapist.patients', compact('pageTitle','therapist','patients'));
    }

    /** GET /therapist/patient/{id} */
    public function patient(int $patientId): void
    {
        $therapist = $this->therapistModel->getByUserId(Session::userId());

        // Verify access
        $patient = $this->db->fetchOne(
            "SELECT p.*, u.first_name, u.last_name, u.email, u.phone, u.date_of_birth
             FROM patients p JOIN users u ON u.id = p.user_id
             WHERE p.id = ? AND p.assigned_therapist = ?",
            [$patientId, Session::userId()]
        );

        if (!$patient) {
            Session::flash('error', 'Patient not found or access denied.');
            $this->redirect('therapist/patients');
        }

        // Load required models
        require_once BASE_PATH . '/app/models/Wellness.php';
        require_once BASE_PATH . '/app/models/SessionRecord.php';

        // Get patient data
        $sessions = (new SessionRecord())->getForTherapist($therapist['id']);
        $patientSessions = array_filter($sessions, fn($s) => $s['patient_id'] == $patientId);

        $moodModel = new MoodEntry();
        $moodData = $moodModel->getLast30Days($patientId);
        $avgMood7 = $moodModel->getAverage($patientId, 7);
        $avgMood30 = $moodModel->getAverage($patientId, 30);

        $goals = (new WellnessGoal())->getAll($patientId);

        $pageTitle = 'Patient Details: ' . $patient['first_name'] . ' ' . $patient['last_name'];
        $this->view('therapist.patient', compact('pageTitle','therapist','patient','patientSessions','moodData','avgMood7','avgMood30','goals'));
    }

    /** GET /therapist/generateReport/{patientId} */
    public function generateReport(int $patientId): void
    {
        $therapist = $this->therapistModel->getByUserId(Session::userId());

        // Verify access
        $patient = $this->db->fetchOne(
            "SELECT p.id FROM patients p WHERE p.id = ? AND p.assigned_therapist = ?",
            [$patientId, Session::userId()]
        );

        if (!$patient) {
            Session::flash('error', 'Patient not found or access denied.');
            $this->redirect('therapist/patients');
        }

        $pageTitle = 'Generate Patient Report';
        $this->view('therapist.generate_report', compact('pageTitle','therapist','patientId'));
    }

    /** POST /therapist/createReport */
    public function createReport(): void
    {
        $patientId = (int)$this->post('patient_id');
        $therapist = $this->therapistModel->getByUserId(Session::userId());

        // Verify access
        $patient = $this->db->fetchOne(
            "SELECT p.id FROM patients p WHERE p.id = ? AND p.assigned_therapist = ?",
            [$patientId, Session::userId()]
        );

        if (!$patient) {
            Session::flash('error', 'Patient not found or access denied.');
            $this->redirect('therapist/patients');
        }

        $options = [
            'start_date' => $this->post('start_date') ?: date('Y-m-d', strtotime('-6 months')),
            'end_date' => $this->post('end_date') ?: date('Y-m-d'),
            'include_sessions' => $this->post('include_sessions') ? true : false,
            'include_mood' => $this->post('include_mood') ? true : false,
            'include_goals' => $this->post('include_goals') ? true : false,
            'summary' => $this->post('summary', ''),
        ];

        try {
            require_once BASE_PATH . '/core/PatientReportPDF.php';
            $pdfGenerator = new PatientReportPDF();
            $pdfGenerator->generateReport($patientId, $therapist['id'], $options);
            // The generateReport method handles the PDF output and download
        } catch (Exception $e) {
            Session::flash('error', 'Error generating report: ' . $e->getMessage());
            $this->redirect("therapist/generateReport/$patientId");
        }
    }

    /** GET /therapist/sessionNotes/{apptId} */
    public function sessionNotes(int $apptId): void
    {
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
