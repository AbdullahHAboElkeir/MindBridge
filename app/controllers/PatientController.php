<?php

class PatientController extends Controller
{
    private Patient $patientModel;

    public function __construct()
    {
        parent::__construct();
        Middleware::requirePatient();
        $this->patientModel = $this->model('Patient');
        require_once BASE_PATH . '/app/models/IntakeForm.php';
    }

    /** GET /patient/profile */
    public function profile(): void
    {
        $patient = $this->patientModel->getByUserId(Session::userId());
        $pageTitle = 'My Profile';
        $this->view('patient.profile', compact('pageTitle', 'patient'));
    }

    /** POST /patient/updateProfile */
    public function updateProfile(): void
    {
        if (!$this->isPost()) { $this->redirect('patient/profile'); }
        $patient = $this->patientModel->getByUserId(Session::userId());
        $data = [
            'first_name'          => $this->post('first_name'),
            'last_name'           => $this->post('last_name'),
            'phone'               => $this->post('phone'),
            'gender'              => $this->post('gender'),
            'date_of_birth'       => $this->post('date_of_birth'),
            'timezone'            => $this->post('timezone', 'UTC'),
            'insurance_provider'  => $this->post('insurance_provider'),
            'insurance_number'    => $this->post('insurance_number'),
            'emergency_contact'   => $this->post('emergency_contact'),
            'emergency_phone'     => $this->post('emergency_phone'),
            'address'             => $this->post('address'),
            'city'                => $this->post('city'),
            'country'             => $this->post('country', 'Egypt'),
            'preferred_language'  => $this->post('preferred_language', 'English'),
        ];

        // Handle avatar upload
        if (!empty($_FILES['avatar']['name'])) {
            $ext  = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif','webp'];
            if (in_array($ext, $allowed) && $_FILES['avatar']['size'] <= MAX_FILE_SIZE) {
                $filename = 'avatar_' . Session::userId() . '_' . time() . '.' . $ext;
                move_uploaded_file($_FILES['avatar']['tmp_name'], UPLOAD_PATH . $filename);
                $userModel = $this->model('User');
                $userModel->updateAvatar(Session::userId(), $filename);
                Session::set('avatar', $filename);
            }
        }

        $this->patientModel->updateProfile($patient['id'], Session::userId(), $data);
        Session::set('first_name', $data['first_name']);
        Session::set('last_name',  $data['last_name']);
        $this->auditLog('update_profile', 'patients', 'Patient profile updated');
        Session::flash('success', 'Profile updated successfully.');
        $this->redirect('patient/profile');
    }

    /** GET /patient/intake */
    public function intake(): void
    {
        $patient = $this->patientModel->getByUserId(Session::userId());
        $intakeModel = $this->model('IntakeForm');
        $form = $intakeModel->getByPatientId($patient['id']);
        $pageTitle = 'Intake Form';
        $this->view('patient.intake_form', compact('pageTitle', 'patient', 'form'));
    }

    /** POST /patient/submitIntake */
    public function submitIntake(): void
    {
        if (!$this->isPost()) { $this->redirect('patient/intake'); }
        $patient = $this->patientModel->getByUserId(Session::userId());
        if (!$patient) { 
            Session::flash('error', 'Patient record not found.');
            $this->redirect('patient/intake'); 
        }
        
        $intakeModel = new IntakeForm();

        $data = [
            'primary_concerns'       => $this->post('primary_concerns'),
            'mental_health_history'  => $this->post('mental_health_history'),
            'current_medications'    => $this->post('current_medications'),
            'previous_therapy'       => $this->post('previous_therapy'),
            'therapy_type_pref'      => $this->post('therapy_type_pref'),
            'therapist_gender_pref'  => $this->post('therapist_gender_pref', 'no_preference'),
            'preferred_language'     => $this->post('preferred_language', 'English'),
            'session_format_pref'    => $this->post('session_format_pref', 'no_preference'),
            'availability_notes'     => $this->post('availability_notes'),
            'urgency_level'          => $this->post('urgency_level', 'medium'),
            'goals'                  => $this->post('goals'),
        ];

        $submit = $this->post('action') === 'submit';
        $intakeModel->save($patient['id'], $data, $submit);

        if ($submit && ($patient['onboarding_step'] ?? 0) < 2) {
            $this->patientModel->updateOnboardingStep($patient['id'], 2);
        }

        $this->auditLog('submit_intake', 'intake_forms', 'Intake form ' . ($submit ? 'submitted' : 'saved as draft'));
        Session::flash('success', $submit ? 'Intake form submitted! Please sign consent forms.' : 'Draft saved.');
        $this->redirect($submit ? 'patient/consent' : 'patient/intake');
    }

    /** GET /patient/consent */
    public function consent(): void
    {
        $patient = $this->patientModel->getByUserId(Session::userId());
        $consentModel = new ConsentForm();
        $forms = $consentModel->getByPatientId($patient['id']);
        $signedTypes = array_column($forms, 'form_type');
        $pageTitle = 'Consent Forms';
        $this->view('patient.consent_form', compact('pageTitle', 'patient', 'forms', 'signedTypes'));
    }

    /** POST /patient/submitConsent */
    public function submitConsent(): void
    {
        if (!$this->isPost()) { $this->redirect('patient/consent'); }
        $consentModel = new ConsentForm();
        $patient = $this->patientModel->getByUserId(Session::userId());

        $type      = $this->post('form_type');
        $signature = $this->post('signature');
        $ip        = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        if ($type && $signature) {
            $consentModel->sign($patient['id'], $type, $signature, $ip);
            $this->auditLog('sign_consent', 'consent_forms', "Signed: $type");
        }

        // Check if all done
        if ($consentModel->isAllSigned($patient['id']) && ($patient['onboarding_step'] ?? 0) < 3) {
            $this->patientModel->updateOnboardingStep($patient['id'], 3);
            Session::flash('success', 'All consent forms signed! Now find your therapist.');
            $this->redirect('patient/matching');
        } else {
            Session::flash('success', 'Form signed successfully.');
            $this->redirect('patient/consent');
        }
    }

    /** GET /patient/matching */
    public function matching(): void
    {
        $patient = $this->patientModel->getByUserId(Session::userId());
        $intakeModel    = new IntakeForm();
        $matchModel     = new TherapistMatch();
        $therapistModel = $this->model('Therapist');

        $intake = $intakeModel->getByPatientId($patient['id']);
        $existingMatches = $matchModel->getForPatient($patient['id']);

        if (empty($existingMatches) && $intake) {
            $matches = $therapistModel->findMatches($intake);
            $matchModel->saveMatches($patient['id'], $matches);
            $existingMatches = $matchModel->getForPatient($patient['id']);
        }

        $pageTitle = 'Find Your Therapist';
        $this->view('patient.matching', compact('pageTitle', 'patient', 'existingMatches', 'intake'));
    }

    /** POST /patient/selectTherapist */
    public function selectTherapist(): void
    {
        if (!$this->isPost()) { $this->redirect('patient/matching'); }
        $matchModel = new TherapistMatch();
        $patient = $this->patientModel->getByUserId(Session::userId());
        $therapistId = (int)$this->post('therapist_id');

        if (!$therapistId) {
            Session::flash('error', 'Please select a therapist.');
            $this->redirect('patient/matching');
        }

        $therapistModel = $this->model('Therapist');
        $therapist = $therapistModel->getById($therapistId);

        if (!$therapist) {
            Session::flash('error', 'Therapist not found.');
            $this->redirect('patient/matching');
        }

        $matchModel->accept($patient['id'], $therapistId);
        $this->patientModel->assignTherapist($patient['id'], $therapist['user_id']);

        // Notify therapist
        $this->db->insert(
            "INSERT INTO notifications (user_id, type, title, message, link, created_at)
             VALUES (?, 'new_patient', 'New Patient Assigned',
             ?, ?, NOW())",
            [$therapist['user_id'],
             $patient['first_name'].' '.$patient['last_name'].' has selected you as their therapist.',
             '/therapist/patients']
        );

        $this->auditLog('select_therapist', 'therapist_matches', "Selected therapist ID: $therapistId");
        Session::flash('success', 'Great! You are now matched with Dr. ' . $therapist['first_name'] . '. Book your first session!');
        $this->redirect('appointments/book');
    }
}
