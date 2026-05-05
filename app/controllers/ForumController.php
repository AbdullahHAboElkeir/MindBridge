<?php

/**
 * Controller: Forum
 * Community forum — list, create, view, edit, delete posts + comments.
 */
class ForumController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Middleware::requireAuth();
        require_once BASE_PATH . '/app/models/Forum.php';
    }

    private function forumModel(): ForumPost
    {
        return new ForumPost();
    }

    // ──────────────────────────────────────────────────────────────
    // POSTS
    // ──────────────────────────────────────────────────────────────

    /** GET /forum */
    public function index(): void
    {
        $model      = $this->forumModel();
        $category   = $this->get('category', '');
        $page       = max(1, (int)$this->get('page', 1));
        $posts      = $model->getAll($category, $page);
        $total      = $model->count($category);
        $pages      = max(1, (int)ceil($total / ITEMS_PER_PAGE));
        $categories = ['general','anxiety','depression','stress','relationships','mindfulness','trauma','grief'];
        $pageTitle  = 'Community Forum';
        $this->view('forum.index', compact('pageTitle','posts','total','pages','page','category','categories'));
    }

    /** GET /forum/create */
    public function create(): void
    {
        $categories = ['general','anxiety','depression','stress','relationships','mindfulness','trauma','grief'];
        $pageTitle  = 'New Post';
        $this->view('forum.create', compact('pageTitle', 'categories'));
    }

    /** POST /forum/store */
    public function store(): void
    {
        if (!$this->isPost()) { $this->redirect('forum/create'); }

        $data = [
            'title'        => $this->post('title'),
            'content'      => $this->post('content'),
            'category'     => $this->post('category', 'general'),
            'is_anonymous' => (bool)$this->post('is_anonymous'),
            'pseudonym'    => $this->post('pseudonym', ''),
        ];

        if (empty($data['title']) || empty($data['content'])) {
            Session::flash('error', 'Title and content are required.');
            $this->redirect('forum/create');
        }

        // Crisis keyword detection
        $this->checkCrisis($data['content'], 'forum');

        $postId = $this->forumModel()->create(Session::userId(), $data);
        $this->auditLog('create_post', 'forum_posts', "Created forum post ID: $postId");
        Session::flash('success', 'Post published successfully!');
        $this->redirect("forum/view/$postId");
    }

    /** GET /forum/view/{id} */
    public function view(int $id): void
    {
        $model    = $this->forumModel();
        $post     = $model->getById($id);
        if (!$post) {
            Session::flash('error', 'Post not found or has been removed.');
            $this->redirect('forum');
        }
        $comments  = $model->getComments($id);
        $pageTitle = htmlspecialchars($post['title']);
        $this->view('forum.view', compact('pageTitle','post','comments'));
    }

    /** GET /forum/edit/{id} */
    public function edit(int $id): void
    {
        $model = $this->forumModel();
        $post  = $model->getForEdit($id);

        if (!$post) {
            Session::flash('error', 'Post not found.');
            $this->redirect('forum');
        }

        $isAdmin = Session::role() === 'admin';
        if (!$isAdmin && (int)$post['user_id'] !== Session::userId()) {
            Session::flash('error', 'You can only edit your own posts.');
            $this->redirect("forum/view/$id");
        }

        $categories = ['general','anxiety','depression','stress','relationships','mindfulness','trauma','grief'];
        $pageTitle  = 'Edit Post';
        $this->view('forum.edit', compact('pageTitle', 'post', 'categories'));
    }

    /** POST /forum/update/{id} */
    public function update(int $id): void
    {
        if (!$this->isPost()) { $this->redirect("forum/edit/$id"); }

        $data = [
            'title'    => $this->post('title'),
            'content'  => $this->post('content'),
            'category' => $this->post('category', 'general'),
        ];

        if (empty($data['title']) || empty($data['content'])) {
            Session::flash('error', 'Title and content are required.');
            $this->redirect("forum/edit/$id");
        }

        $isAdmin = Session::role() === 'admin';
        $model   = $this->forumModel();

        if ($isAdmin) {
            // Admin: bypass user_id guard by fetching then updating directly
            $post = $model->getForEdit($id);
            if ($post) {
                $model->update($id, (int)$post['user_id'], $data);
            }
        } else {
            $model->update($id, Session::userId(), $data);
        }

        $this->auditLog('edit_post', 'forum_posts', "Edited post ID: $id");
        Session::flash('success', 'Post updated successfully.');
        $this->redirect("forum/view/$id");
    }

    /** POST /forum/delete/{id} */
    public function delete(int $id): void
    {
        if (!$this->isPost()) { $this->redirect('forum'); }

        $isAdmin = Session::role() === 'admin';
        $result  = $this->forumModel()->softDelete($id, Session::userId(), $isAdmin);

        if ($result) {
            $this->auditLog('delete_post', 'forum_posts', "Deleted post ID: $id");
            Session::flash('success', 'Post removed.');
        } else {
            Session::flash('error', 'Could not delete post — you may not have permission.');
        }
        $this->redirect('forum');
    }

    // ──────────────────────────────────────────────────────────────
    // COMMENTS
    // ──────────────────────────────────────────────────────────────

    /** POST /forum/comment */
    public function comment(): void
    {
        if (!$this->isPost()) { $this->redirect('forum'); }

        $postId = (int)$this->post('post_id');
        $data   = [
            'content'      => $this->post('content'),
            'is_anonymous' => (bool)$this->post('is_anonymous'),
            'pseudonym'    => $this->post('pseudonym', ''),
        ];

        if (empty($data['content'])) {
            Session::flash('error', 'Comment cannot be empty.');
            $this->redirect("forum/view/$postId");
        }

        // Crisis keyword detection on comment
        $this->checkCrisis($data['content'], 'forum');

        $this->forumModel()->addComment($postId, Session::userId(), $data);
        Session::flash('success', 'Comment added.');
        $this->redirect("forum/view/$postId");
    }

    /** POST /forum/deleteComment/{id} */
    public function deleteComment(int $id): void
    {
        if (!$this->isPost()) { $this->redirect('forum'); }

        $postId  = (int)$this->post('post_id');
        $isAdmin = Session::role() === 'admin';
        $result  = $this->forumModel()->deleteComment($id, Session::userId(), $isAdmin);

        if ($result) {
            $this->auditLog('delete_comment', 'forum_comments', "Deleted comment ID: $id");
            Session::flash('success', 'Comment removed.');
        } else {
            Session::flash('error', 'Could not remove comment — you may not have permission.');
        }
        $this->redirect($postId ? "forum/view/$postId" : 'forum');
    }

    // ──────────────────────────────────────────────────────────────
    // REPORTS
    // ──────────────────────────────────────────────────────────────

    /** POST /forum/report */
    public function report(): void
    {
        if (!$this->isPost()) { $this->redirect('forum'); }

        $type     = $this->post('type');
        $targetId = (int)$this->post('target_id');
        $reason   = $this->post('reason', 'other');
        $details  = $this->post('details', '');

        try {
            $this->db->insert(
                "INSERT INTO reports (reporter_id, type, target_id, reason, details, status, created_at)
                 VALUES (?,?,?,?,?,'pending',NOW())",
                [Session::userId(), $type, $targetId, $reason, $details]
            );
            Session::flash('success', 'Report submitted. Our moderation team will review it.');
        } catch (Exception $e) {
            Session::flash('error', 'Could not submit report. Please try again.');
        }

        $this->redirect('forum');
    }

    // ──────────────────────────────────────────────────────────────
    // INTERNAL — Crisis keyword detection helper
    // ──────────────────────────────────────────────────────────────

    private function checkCrisis(string $text, string $source): void
    {
        if (Session::role() !== 'patient') return;

        $lower = strtolower($text);
        foreach (CRISIS_KEYWORDS as $kw) {
            if (str_contains($lower, $kw)) {
                try {
                    $patient = $this->db->fetchOne(
                        "SELECT id FROM patients WHERE user_id=?",
                        [Session::userId()]
                    );
                    if (!$patient) return;

                    $this->db->insert(
                        "INSERT INTO crisis_alerts
                            (patient_id, trigger_text, source, severity, status, created_at)
                         VALUES (?,?,?,'medium','new',NOW())",
                        [$patient['id'], substr($text, 0, 500), $source]
                    );

                    // Notify all active admins
                    $admins = $this->db->fetchAll(
                        "SELECT id FROM users WHERE role='admin' AND status='active'"
                    );
                    foreach ($admins as $admin) {
                        $this->db->insert(
                            "INSERT INTO notifications
                                (user_id, type, title, message, link, created_at)
                             VALUES (?,'crisis_alert','🚨 Crisis Alert',
                                     'A patient triggered a crisis keyword in the $source.',
                                     '/crisis',NOW())",
                            [$admin['id']]
                        );
                    }
                } catch (Exception $e) {
                    // Never break posting flow for a crisis alert failure
                }
                break; // only one alert per submission
            }
        }
    }
}
