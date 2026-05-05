<?php

class AdminController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Middleware::requireAdmin();
    }

    /** GET /admin/users */
    public function users(): void
    {
        $page    = (int)$this->get('page', 1);
        $role    = $this->get('role', '');
        $search  = $this->get('search', '');
        $userModel = $this->model('User');

        if ($search) {
            $users = $userModel->search($search);
            $total = count($users);
        } else {
            $users = $userModel->getAll($role, $page);
            $total = $userModel->count($role);
        }
        $pages   = max(1, ceil($total / 15));
        $pageTitle = 'User Management';
        $this->view('admin.users', compact('pageTitle','users','total','pages','page','role','search'));
    }

    /** POST /admin/manageUser/{id} */
    public function manageUser(int $id): void
    {
        $action    = $this->post('action');
        $userModel = $this->model('User');
        match ($action) {
            'suspend'  => $userModel->updateStatus($id, 'suspended'),
            'activate' => $userModel->updateStatus($id, 'active'),
            default    => null,
        };
        $this->auditLog('admin_user_action','users',"Action: $action on user ID: $id");
        Session::flash('success', 'User status updated.');
        $this->redirect('admin/users');
    }

    /** GET /admin/reports */
    public function reports(): void
    {
        $status  = $this->get('status', 'pending');
        $reports = $this->db->fetchAll(
            "SELECT r.*, u.first_name AS reporter_first, u.last_name AS reporter_last
             FROM reports r JOIN users u ON u.id = r.reporter_id
             WHERE r.status = ? ORDER BY r.created_at DESC",
            [$status]);
        $pageTitle = 'Content Reports';
        $this->view('admin.reports', compact('pageTitle','reports','status'));
    }

    /** POST /admin/resolveReport */
    public function resolveReport(): void
    {
        $id     = (int)$this->post('report_id');
        $action = $this->post('action', 'dismiss');
        $status = ($action === 'action_taken') ? 'resolved' : 'dismissed';
        $this->db->execute(
            "UPDATE reports SET status=?, reviewed_by=?, reviewed_at=NOW() WHERE id=?",
            [$status, Session::userId(), $id]);
        $this->auditLog('resolve_report', 'reports', "Report ID: $id action: $action");
        Session::flash('success', 'Report updated.');
        $this->redirect('admin/reports');
    }

    /** GET /admin/moderation */
    public function moderation(): void
    {
        require_once BASE_PATH . '/app/models/Forum.php';
        $pending   = (new ForumPost())->getPending();
        $pageTitle = 'Content Moderation';
        $this->view('admin.moderation', compact('pageTitle','pending'));
    }

    /** POST /admin/moderatePost */
    public function moderatePost(): void
    {
        require_once BASE_PATH . '/app/models/Forum.php';
        $postId = (int)$this->post('post_id');
        $action = $this->post('action', 'publish');
        (new ForumPost())->updateStatus($postId, $action === 'approve' ? 'published' : 'removed');
        $this->auditLog('moderate_post','forum_posts',"Post ID: $postId action: $action");
        Session::flash('success', 'Post moderated.');
        $this->redirect('admin/moderation');
    }

    /** GET /admin/resources */
    public function resources(): void
    {
        require_once BASE_PATH . '/app/models/Wellness.php';
        $resources = (new WellnessResource())->getAll();
        $pageTitle = 'Wellness Resources';
        $this->view('admin.resources', compact('pageTitle','resources'));
    }

    /** POST /admin/storeResource */
    public function storeResource(): void
    {
        require_once BASE_PATH . '/app/models/Wellness.php';
        $id   = (int)$this->post('resource_id', 0);
        $data = [
            'title'       => $this->post('title'),
            'description' => $this->post('description'),
            'content'     => $this->post('content'),
            'type'        => $this->post('type', 'article'),
            'category'    => $this->post('category', 'general'),
            'is_featured' => $this->post('is_featured') ? 1 : 0,
            'is_active'   => $this->post('is_active')   ? 1 : 0,
        ];
        if (empty($data['title'])) {
            Session::flash('error', 'Title is required.');
            $this->redirect('admin/resources');
        }
        (new WellnessResource())->save($data, $id);
        $this->auditLog('manage_resource','wellness_resources', $id ? "Updated resource ID: $id" : 'Created new resource');
        Session::flash('success', $id ? 'Resource updated.' : 'Resource created.');
        $this->redirect('admin/resources');
    }

    /** POST /admin/deleteResource/{id} */
    public function deleteResource(int $id): void
    {
        require_once BASE_PATH . '/app/models/Wellness.php';
        (new WellnessResource())->delete($id);
        $this->auditLog('delete_resource','wellness_resources',"Deleted resource ID: $id");
        Session::flash('success', 'Resource deleted.');
        $this->redirect('admin/resources');
    }

    /** GET /admin/analytics */
    public function analytics(): void
    {
        $db = $this->db;

        $sessMonthly = $db->fetchAll(
            "SELECT DATE_FORMAT(scheduled_at,'%Y-%m') AS ym, COUNT(*) AS cnt
             FROM appointments WHERE scheduled_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
             GROUP BY ym ORDER BY ym ASC");

        $regMonthly = $db->fetchAll(
            "SELECT DATE_FORMAT(created_at,'%Y-%m') AS ym, COUNT(*) AS cnt
             FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
             GROUP BY ym ORDER BY ym ASC");

        $revMonthly = $db->fetchAll(
            "SELECT DATE_FORMAT(paid_at,'%Y-%m') AS ym, SUM(amount) AS total
             FROM payments WHERE status='paid' AND paid_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
             GROUP BY ym ORDER BY ym ASC");

        $moodDist = $db->fetchAll(
            "SELECT CASE
               WHEN mood_level <= 3 THEN 'Low (1-3)'
               WHEN mood_level <= 6 THEN 'Medium (4-6)'
               ELSE 'High (7-10)'
             END AS band, COUNT(*) AS cnt
             FROM mood_entries GROUP BY band");

        $pageTitle = 'Analytics';
        $this->view('admin.analytics', compact('pageTitle','sessMonthly','regMonthly','revMonthly','moodDist'));
    }

    /** GET /admin/auditLogs */
    public function auditLogs(): void
    {
        $page   = (int)$this->get('page', 1);
        $limit  = 20;
        $offset = ($page - 1) * $limit;

        // Use LEFT JOIN — user_id can be NULL for system actions
        $logs = $this->db->fetchAll(
            "SELECT al.*,
                    COALESCE(u.first_name, 'System') AS first_name,
                    COALESCE(u.last_name,  '')        AS last_name
             FROM audit_logs al
             LEFT JOIN users u ON u.id = al.user_id
             ORDER BY al.created_at DESC
             LIMIT ? OFFSET ?",
            [$limit, $offset]);

        $total = (int)($this->db->fetchOne("SELECT COUNT(*) AS c FROM audit_logs")['c'] ?? 0);
        $pages = max(1, ceil($total / $limit));
        $pageTitle = 'Audit Logs';
        $this->view('admin.audit_logs', compact('pageTitle','logs','total','pages','page'));
    }

    /** GET /admin/disputes */
    public function disputes(): void
    {
        $status   = $this->get('status', 'open');
        $disputes = $this->db->fetchAll(
            "SELECT d.*,
                    uf.first_name AS filer_first,  uf.last_name AS filer_last,
                    ua.first_name AS against_first, ua.last_name AS against_last
             FROM disputes d
             JOIN users uf ON uf.id = d.filed_by
             JOIN users ua ON ua.id = d.against
             WHERE d.status = ?
             ORDER BY d.created_at DESC",
            [$status]);

        $counts = [
            'open'         => (int)($this->db->fetchOne("SELECT COUNT(*) AS c FROM disputes WHERE status='open'")['c'] ?? 0),
            'under_review' => (int)($this->db->fetchOne("SELECT COUNT(*) AS c FROM disputes WHERE status='under_review'")['c'] ?? 0),
            'resolved'     => (int)($this->db->fetchOne("SELECT COUNT(*) AS c FROM disputes WHERE status='resolved'")['c'] ?? 0),
        ];

        $pageTitle = 'Disputes';
        $this->view('admin.disputes', compact('pageTitle','disputes','status','counts'));
    }

    /** POST /admin/resolveDispute */
    public function resolveDispute(): void
    {
        $id         = (int)$this->post('dispute_id');
        $action     = $this->post('action', 'resolved');
        $resolution = $this->post('resolution', '');

        $this->db->execute(
            "UPDATE disputes SET status=?, resolution=?, resolved_by=?, resolved_at=NOW() WHERE id=?",
            [$action, $resolution, Session::userId(), $id]);

        $this->auditLog('resolve_dispute','disputes',"Dispute ID: $id resolved as: $action");
        Session::flash('success', 'Dispute updated.');
        $this->redirect('admin/disputes');
    }
}
