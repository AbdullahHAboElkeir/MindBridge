<?php
class ForumController extends Controller
{
    public function index(): void
    {
        $this->authorize(['patient', 'therapist', 'admin']);
        $forums = (new Forum())->topics();
        $this->view->render('forum/index', ['forums' => $forums]);
    }

    public function view(): void
    {
        $this->authorize(['patient', 'therapist', 'admin']);
        $id = (int)($_GET['id'] ?? 0);
        $forumModel = new Forum();
        $forum = current(array_filter($forumModel->topics(), fn($topic) => $topic['id'] === $id));
        if (!$forum) {
            $this->view->render('errors/404');
            return;
        }
        $posts = (new Post())->listByForum($id);
        $this->view->render('forum/view', ['forum' => $forum, 'posts' => $posts]);
    }

    public function post(): void
    {
        $this->authorize(['patient', 'therapist', 'admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $forumId = (int)($_POST['forum_id'] ?? 0);
            (new Post())->add([
                'forum_id' => $forumId,
                'user_id' => $this->auth->user()['id'],
                'content' => trim($_POST['content'] ?? ''),
                'is_anonymous' => isset($_POST['anonymous']),
            ]);
            AuditLog::record($this->auth->user()['id'], 'forum.post', 'Added a forum post.');
            $this->redirect($this->config['app']['base_url'] . '?controller=forum&action=view&id=' . $forumId);
        }
    }

    public function comment(): void
    {
        $this->authorize(['patient', 'therapist', 'admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postId = (int)($_POST['post_id'] ?? 0);
            (new Comment())->add([
                'post_id' => $postId,
                'user_id' => $this->auth->user()['id'],
                'content' => trim($_POST['content'] ?? ''),
                'is_anonymous' => isset($_POST['anonymous']),
            ]);
            AuditLog::record($this->auth->user()['id'], 'forum.comment', 'Added a comment.');
            $this->redirect($this->config['app']['base_url'] . '?controller=forum&action=view&id=' . (int)($_POST['forum_id'] ?? 0));
        }
    }
}
