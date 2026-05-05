<?php

class NotificationController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Middleware::requireAuth();
    }

    /** GET /notifications */
    public function index(): void
    {
        $userId = Session::userId();
        $this->db->execute("UPDATE notifications SET is_read=1, read_at=NOW() WHERE user_id=? AND is_read=0", [$userId]);
        $notifications = $this->db->fetchAll(
            "SELECT * FROM notifications WHERE user_id=? ORDER BY created_at DESC LIMIT 40", [$userId]);
        $pageTitle = 'Notifications';
        $this->view('notifications.index', compact('pageTitle','notifications'));
    }

    /** GET /notifications/poll — AJAX */
    public function poll(): void
    {
        $userId = Session::userId();
        $count  = (int)($this->db->fetchOne(
            "SELECT COUNT(*) AS c FROM notifications WHERE user_id=? AND is_read=0", [$userId])['c'] ?? 0);
        $recent = $this->db->fetchAll(
            "SELECT id, type, title, message, link, created_at FROM notifications
             WHERE user_id=? AND is_read=0 ORDER BY created_at DESC LIMIT 5", [$userId]);
        $this->json(['count' => $count, 'recent' => $recent]);
    }
}
