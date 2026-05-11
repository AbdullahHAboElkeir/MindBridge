<?php

class FeedbackController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Middleware::requireAuth();
    }

    /** GET /feedback/create/{appointmentId} */
    public function create(int $appointmentId): void
    {
        Middleware::requirePatient();
        $appt = $this->db->fetchOne(
            "SELECT a.*, tu.first_name AS t_first, tu.last_name AS t_last, t.id AS therapist_db_id
             FROM appointments a JOIN therapists t ON t.id=a.therapist_id JOIN users tu ON tu.id=t.user_id
             WHERE a.id=? AND a.status='completed'", [$appointmentId]);
        if (!$appt) { $this->redirect('appointments'); }

        $existing = $this->db->fetchOne(
            "SELECT id FROM feedback WHERE appointment_id=?", [$appointmentId]);
        if ($existing) {
            Session::flash('info', 'You already rated this session.');
            $this->redirect('appointments');
        }
        $pageTitle = 'Rate Your Session';
        $this->view('feedback.create', compact('pageTitle', 'appt'));
    }

    /** POST /feedback/store */
    public function store(): void
    {
        Middleware::requirePatient();
        if (!$this->isPost()) { $this->redirect('appointments'); }

        $apptId       = (int)$this->post('appointment_id');
        $therapistId  = (int)$this->post('therapist_id');
        $rating       = max(1, min(5, (int)$this->post('rating', 5)));
        $comment      = $this->post('review', '');   // form field is "review"
        $isAnonymous  = $this->post('is_anonymous') ? 0 : 1;

        $patient = $this->db->fetchOne("SELECT id FROM patients WHERE user_id=?", [Session::userId()]);
        if (!$patient) {
            Session::flash('error', 'Patient record not found.');
            $this->redirect('appointments');
        }

        // Prevent duplicate feedback
        $exists = $this->db->fetchOne("SELECT id FROM feedback WHERE appointment_id=?", [$apptId]);
        if ($exists) {
            Session::flash('info', 'You already submitted feedback for this session.');
            $this->redirect('appointments');
        }

        $this->db->insert(
            "INSERT INTO feedback (appointment_id, therapist_id, patient_id, rating, comment, is_public, created_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW())",
            [$apptId, $therapistId, $patient['id'], $rating, $comment, $isAnonymous]);

        // Notify therapist that new feedback has been submitted
        $this->db->insert(
            "INSERT INTO notifications (user_id, type, title, message, link, created_at)
             VALUES (?, 'feedback_received', ?, ?, ?, NOW())",
            [
                (int)$this->db->fetchOne("SELECT user_id FROM therapists WHERE id = ?", [$therapistId])['user_id'],
                'New patient feedback received',
                'A patient submitted a new rating and review for you.',
                '/feedback'
            ]
        );

        // Update therapist aggregate rating
        $this->db->execute(
            "UPDATE therapists t SET
               rating       = (SELECT COALESCE(AVG(f.rating),0) FROM feedback f WHERE f.therapist_id = t.id),
               total_reviews= (SELECT COUNT(*) FROM feedback f WHERE f.therapist_id = t.id)
             WHERE t.id = ?", [$therapistId]);

        $this->auditLog('submit_feedback', 'feedback', "Rated therapist ID: $therapistId — $rating/5");
        Session::flash('success', 'Thank you for your feedback!');
        $this->redirect('appointments');
    }

    /** GET /feedback — therapist views their own reviews */
    public function index(): void
    {
        Middleware::requireTherapist();
        $therapist = $this->db->fetchOne("SELECT id FROM therapists WHERE user_id=?", [Session::userId()]);
        if (!$therapist) { $this->redirect('dashboard'); }

        $reviews = $this->db->fetchAll(
            "SELECT f.*, u.first_name, u.last_name, a.scheduled_at
             FROM feedback f
             JOIN patients p ON p.id = f.patient_id
             JOIN users u ON u.id = p.user_id
             JOIN appointments a ON a.id = f.appointment_id
             WHERE f.therapist_id = ?
             ORDER BY f.created_at DESC",
            [$therapist['id']]);

        $avgRating = $this->db->fetchOne(
            "SELECT COALESCE(AVG(rating),0) AS avg, COUNT(*) AS cnt FROM feedback WHERE therapist_id=?",
            [$therapist['id']]);

        $pageTitle = 'My Reviews';
        $this->view('feedback.index', compact('pageTitle', 'reviews', 'avgRating'));
    }
}
