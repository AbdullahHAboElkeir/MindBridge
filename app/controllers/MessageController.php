<?php

class MessageController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Middleware::requireAuth();
    }

    /** GET /messages */
    public function index(): void
    {
        $msgModel      = $this->model('Message');
        $userId        = Session::userId();
        $conversations = $msgModel->getConversations($userId);
        $contacts      = $msgModel->getContacts($userId);
        $activeUserId  = (int)$this->get('with', 0);
        $thread        = [];
        $activeUser    = null;

        if ($activeUserId) {
            $thread     = $msgModel->getThread($userId, $activeUserId);
            $activeUser = $this->db->fetchOne(
                "SELECT id, first_name, last_name, role, avatar FROM users WHERE id=?", [$activeUserId]);
        }

        $pageTitle = 'Messages';
        $this->view('messages.index', compact('pageTitle','conversations','contacts','thread','activeUser','activeUserId'));
    }

    /** POST /messages/send */
    public function send(): void
    {
        if (!$this->isPost()) { $this->redirect('messages'); }

        $receiverId = (int)$this->post('receiver_id');
        $content    = $this->post('content', '');
        $subject    = $this->post('subject', '');

        if (!$receiverId || empty($content)) {
            Session::flash('error', 'Recipient and message are required.');
            $this->redirect('messages');
        }

        // Crisis detection
        $text = strtolower($content);
        foreach (CRISIS_KEYWORDS as $kw) {
            if (str_contains($text, $kw) && Session::role() === 'patient') {
                $patient = $this->db->fetchOne("SELECT id FROM patients WHERE user_id=?", [Session::userId()]);
                if ($patient) {
                    $this->db->insert(
                        "INSERT INTO crisis_alerts (patient_id, trigger_text, source, severity, status, created_at)
                         VALUES (?,?,'message','high','new',NOW())",
                        [$patient['id'], substr($content, 0, 500)]);
                }
                break;
            }
        }

        $msgModel = $this->model('Message');
        $msgModel->send(Session::userId(), $receiverId, $content, $subject);

        // Notify receiver — use parameterized query, no string concatenation
        $senderName = Session::get('first_name', '') . ' ' . Session::get('last_name', '');
        $msgLink    = '/messages?with=' . Session::userId();
        $this->db->insert(
            "INSERT INTO notifications (user_id, type, title, message, link, created_at)
             VALUES (?, 'new_message', 'New Message', ?, ?, NOW())",
            [$receiverId, 'You have a new message from ' . $senderName . '.', $msgLink]);

        if ($this->isAjax()) {
            $this->json(['success' => true, 'message' => 'Message sent.']);
        }
        $this->redirect("messages?with=$receiverId");
    }

    /** GET /messages/count — AJAX for notification badge */
    public function count(): void
    {
        $msgModel = $this->model('Message');
        $count = $msgModel->getUnreadCount(Session::userId());
        $this->json(['count' => $count]);
    }
}
