<?php

class CrisisController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Middleware::requireAuth();
    }

    /** POST /crisis/detect — AJAX endpoint from JS */
    public function detect(): void
    {
        if (!$this->isAjax()) { $this->redirect('dashboard'); }
        if (Session::role() !== 'patient') { $this->json(['ok' => true]); }

        $patient = $this->db->fetchOne("SELECT id FROM patients WHERE user_id=?", [Session::userId()]);
        if (!$patient) { 
            // Log error for debugging
            error_log("Crisis alert - Patient not found for user: " . Session::userId());
            $this->json(['ok' => true, 'message' => 'Alert recorded']); 
        }

        $text = $this->post('trigger_text', '');
        $this->db->insert(
            "INSERT INTO crisis_alerts (patient_id, trigger_text, source, severity, status, created_at)
             VALUES (?,?,'journal','high','new',NOW())",
            [$patient['id'], substr($text, 0, 500)]);

        // Notify all admins
        $admins = $this->db->fetchAll("SELECT id FROM users WHERE role='admin' AND status='active'");
        foreach ($admins as $admin) {
            $this->db->insert(
                "INSERT INTO notifications (user_id, type, title, message, link, created_at)
                 VALUES (?,'crisis_alert','🚨 Crisis Alert Detected','A patient has triggered a crisis keyword. Immediate review required.','/crisis',NOW())",
                [$admin['id']]);
        }

        $this->json(['ok' => true, 'message' => 'Alert recorded.']);
    }

    /** GET /crisis — Admin: list all alerts */
    public function index(): void
    {
        Middleware::requireAdmin();
        $status = $this->get('status', 'new');
        $alerts = $this->db->fetchAll(
            "SELECT ca.*, u.first_name, u.last_name, u.email
             FROM crisis_alerts ca
             JOIN patients p ON p.id=ca.patient_id
             JOIN users u ON u.id=p.user_id
             WHERE ca.status=?
             ORDER BY ca.created_at DESC",
            [$status]);
        $counts = [
            'new'         => (int)($this->db->fetchOne("SELECT COUNT(*) AS c FROM crisis_alerts WHERE status='new'")['c'] ?? 0),
            'acknowledged'=> (int)($this->db->fetchOne("SELECT COUNT(*) AS c FROM crisis_alerts WHERE status='acknowledged'")['c'] ?? 0),
            'in_progress' => (int)($this->db->fetchOne("SELECT COUNT(*) AS c FROM crisis_alerts WHERE status='in_progress'")['c'] ?? 0),
            'resolved'    => (int)($this->db->fetchOne("SELECT COUNT(*) AS c FROM crisis_alerts WHERE status='resolved'")['c'] ?? 0),
        ];
        $pageTitle = 'Crisis Alerts';
        $this->view('admin.crisis_alerts', compact('pageTitle','alerts','status','counts'));
    }

    /** GET /crisis/respond/{id} */
    public function respond(int $id): void
    {
        Middleware::requireAdmin();
        $alert = $this->db->fetchOne(
            "SELECT ca.*, u.first_name, u.last_name, u.email
             FROM crisis_alerts ca JOIN patients p ON p.id=ca.patient_id JOIN users u ON u.id=p.user_id
             WHERE ca.id=?", [$id]);
        if (!$alert) { $this->redirect('crisis'); }
        // Mark acknowledged
        $this->db->execute(
            "UPDATE crisis_alerts SET status='acknowledged', responder_id=? WHERE id=?",
            [Session::userId(), $id]);
        $pageTitle = 'Respond to Crisis';
        $this->view('admin.crisis_respond', compact('pageTitle','alert'));
    }

    /** POST /crisis/resolve */
    public function resolve(): void
    {
        Middleware::requireAdmin();
        $id   = (int)$this->post('alert_id');
        $note = $this->post('response_note', '');
        $this->db->execute(
            "UPDATE crisis_alerts SET status='resolved', response_note=?, resolved_at=NOW(), responder_id=? WHERE id=?",
            [$note, Session::userId(), $id]);
        $this->auditLog('resolve_crisis','crisis_alerts',"Resolved alert ID: $id");
        Session::flash('success', 'Crisis alert resolved.');
        $this->redirect('crisis');
    }
}
