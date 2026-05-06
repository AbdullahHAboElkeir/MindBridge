<?php

/**
 * Controller: Sessions
 * Shows completed therapy session records.
 * Route: /sessions  →  SessionsController::index()
 */
class SessionsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Middleware::requireAuth();
    }

    /** GET /sessions */
    public function index(): void
    {
        $role   = Session::role();
        $userId = Session::userId();

        if ($role === 'patient') {
            $patient = $this->db->fetchOne(
                "SELECT id FROM patients WHERE user_id = ?", [$userId]);
            if (!$patient) { $this->redirect('dashboard'); }

            $sessions = $this->db->fetchAll(
                "SELECT s.*,
                        a.scheduled_at, a.type, a.status AS appt_status,
                        tu.first_name AS t_first, tu.last_name AS t_last
                 FROM sessions s
                 JOIN appointments a  ON a.id  = s.appointment_id
                 JOIN therapists  t  ON t.id  = a.therapist_id
                 JOIN users       tu ON tu.id = t.user_id
                 WHERE a.patient_id = ?
                 ORDER BY a.scheduled_at DESC",
                [$patient['id']]);

        } elseif ($role === 'therapist') {
            $therapist = $this->db->fetchOne(
                "SELECT id FROM therapists WHERE user_id = ?", [$userId]);
            if (!$therapist) { $this->redirect('dashboard'); }

            $sessions = $this->db->fetchAll(
                "SELECT s.*,
                        a.scheduled_at, a.type, a.status AS appt_status,
                        pu.first_name AS p_first, pu.last_name AS p_last
                 FROM sessions s
                 JOIN appointments a  ON a.id  = s.appointment_id
                 JOIN patients    p  ON p.id  = a.patient_id
                 JOIN users       pu ON pu.id = p.user_id
                 WHERE a.therapist_id = ?
                 ORDER BY a.scheduled_at DESC",
                [$therapist['id']]);

        } else {
            // Admin: all sessions
            $sessions = $this->db->fetchAll(
                "SELECT s.*,
                        a.scheduled_at, a.type, a.status AS appt_status,
                        pu.first_name AS p_first, pu.last_name AS p_last,
                        tu.first_name AS t_first, tu.last_name AS t_last
                 FROM sessions s
                 JOIN appointments a  ON a.id  = s.appointment_id
                 JOIN patients    p  ON p.id  = a.patient_id
                 JOIN users       pu ON pu.id = p.user_id
                 JOIN therapists  t  ON t.id  = a.therapist_id
                 JOIN users       tu ON tu.id = t.user_id
                 ORDER BY a.scheduled_at DESC
                 LIMIT 100");
        }

        $pageTitle = 'My Sessions';
        $this->view('sessions.index', compact('pageTitle', 'sessions', 'role'));
    }

    /**
     * GET /sessions/book — redirect to appointment booking
     * Keeps /sessions/book as a valid URL that doesn't 404
     */
    public function book(): void
    {
        Middleware::requirePatient();
        $this->redirect('appointments/book');
    }

    /**
     * GET /sessions/schedule — for therapists, show availability page
     */
    public function schedule(): void
    {
        $role = Session::role();
        if ($role === 'therapist') {
            $this->redirect('therapist/availability');
        } elseif ($role === 'patient') {
            $this->redirect('appointments/book');
        } else {
            $this->redirect('dashboard');
        }
    }
}
