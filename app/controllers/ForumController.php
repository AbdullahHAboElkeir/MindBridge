<?php

class ForumController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Middleware::requireAuth();
    }

    private function forumModel(): ForumPost
    {
        require_once BASE_PATH . '/app/models/Forum.php';
        return new ForumPost();
    }

    /** GET /forum */
    public function index(): void
    {
        $model    = $this->forumModel();
        $category = $this->get('category', '');
        $page     = (int)$this->get('page', 1);
        $posts    = $model->getAll($category, $page);
        $total    = $model->count($category);
        $pages    = max(1, ceil($total / ITEMS_PER_PAGE));
        $categories = ['general','anxiety','depression','stress','relationships','mindfulness','trauma','grief'];
        $pageTitle = 'Community Forum';
        $this->view('forum.index', compact('pageTitle','posts','total','pages','page','category','categories'));
    }

    /** GET /forum/create */
    public function create(): void
    {
        $pageTitle = 'New Post';
        $this->view('forum.create', compact('pageTitle'));
    }

    /** POST /forum/store */
    public function store(): void
    {
        if (!$this->isPost()) { $this->redirect('forum/create'); }

        $data = [
            'title'        => $this->post('title'),
            'content'      => $this->post('content'),
            'category'     => $this->post('category', 'general'),
            'is_anonymous' => $this->post('is_anonymous'),
            'pseudonym'    => $this->post('pseudonym', ''),
        ];

        if (empty($data['title']) || empty($data['content'])) {
            Session::flash('error', 'Title and content are required.');
            $this->redirect('forum/create');
        }

        // Crisis check
        $text = strtolower($data['content']);
        foreach (CRISIS_KEYWORDS as $kw) {
            if (str_contains($text, $kw) && Session::role() === 'patient') {
                $patient = $this->db->fetchOne("SELECT id FROM patients WHERE user_id=?", [Session::userId()]);
                if ($patient) {
                    $this->db->insert(
                        "INSERT INTO crisis_alerts (patient_id, trigger_text, source, severity, status, created_at)
                         VALUES (?,?,'forum','medium','new',NOW())",
                        [$patient['id'], substr($data['content'], 0, 500)]);
                }
                break;
            }
        }

        $postId = $this->forumModel()->create(Session::userId(), $data);
        $this->auditLog('create_post', 'forum_posts', "Created forum post ID: $postId");
        Session::flash('success', 'Post published successfully!');
        $this->redirect("forum/view/$postId");
    }

    /** GET /forum/view/{id} */
    public function view(int $id): void
    {
        $model   = $this->forumModel();
        $post    = $model->getById($id);
        if (!$post) { $this->redirect('forum'); }
        $comments  = $model->getComments($id);
        $pageTitle = $post['title'];
        $this->view('forum.view', compact('pageTitle','post','comments'));
    }

    /** POST /forum/comment */
    public function comment(): void
    {
        if (!$this->isPost()) { $this->redirect('forum'); }
        $postId = (int)$this->post('post_id');
        $data   = [
            'content'      => $this->post('content'),
            'is_anonymous' => $this->post('is_anonymous'),
            'pseudonym'    => $this->post('pseudonym', ''),
        ];
        if (empty($data['content'])) {
            Session::flash('error', 'Comment cannot be empty.');
            $this->redirect("forum/view/$postId");
        }
        $this->forumModel()->addComment($postId, Session::userId(), $data);
        Session::flash('success', 'Comment added.');
        $this->redirect("forum/view/$postId");
    }

    /** POST /forum/report */
    public function report(): void
    {
        if (!$this->isPost()) { $this->redirect('forum'); }
        $type     = $this->post('type');
        $targetId = (int)$this->post('target_id');
        $reason   = $this->post('reason', 'other');
        $details  = $this->post('details', '');

        $this->db->insert(
            "INSERT INTO reports (reporter_id, type, target_id, reason, details, status, created_at)
             VALUES (?,?,?,?,?,'pending',NOW())",
            [Session::userId(), $type, $targetId, $reason, $details]);

        Session::flash('success', 'Report submitted. Our team will review it.');
        $this->redirect('forum');
    }
}
