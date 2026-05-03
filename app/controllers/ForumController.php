<?php
// app/controllers/ForumController.php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/Forum.php';
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/Comment.php';

class ForumController extends Controller {
    public function index() {
        $this->requireLogin();

        $forumModel = new Forum();
        $forums = $forumModel->findAll();

        // Get posts for each forum
        foreach ($forums as &$forum) {
            $forum['posts'] = $forumModel->getPosts($forum['id']);
        }

        $this->render('forum/index', ['forums' => $forums]);
    }

    public function create() {
        $this->requireLogin();
        $this->render('forum/create');
    }

    public function store() {
        $this->requireLogin();
        $user = $this->getCurrentUser();

        $data = [
            'title' => $_POST['title'] ?? '',
            'description' => $_POST['description'] ?? '',
            'category' => $_POST['category'] ?? 'general'
        ];

        $forumModel = new Forum();
        $forumId = $forumModel->create($data);

        if ($forumId) {
            $_SESSION['success'] = 'Forum created successfully';
            $this->redirect('/forum');
        } else {
            $_SESSION['error'] = 'Failed to create forum';
            $this->redirect('/forum/create');
        }
    }

    public function show() {
        $this->requireLogin();
        $id = $_GET['id'] ?? 0;

        $forumModel = new Forum();
        $forum = $forumModel->find($id);

        if (!$forum) {
            $this->redirect('/forum');
        }

        $forum['posts'] = $forumModel->getPosts($id);

        $this->render('forum/show', ['forum' => $forum]);
    }

    public function storeComment() {
        $this->requireLogin();
        $user = $this->getCurrentUser();

        $postId = $_POST['post_id'] ?? 0;
        $data = [
            'post_id' => $postId,
            'user_id' => $user['id'],
            'content' => $_POST['content'] ?? '',
            'is_anonymous' => isset($_POST['is_anonymous']) ? 1 : 0
        ];

        $commentModel = new Comment();
        $commentModel->create($data);

        // AJAX response
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            echo json_encode(['success' => true]);
            exit;
        }

        $this->redirect('/forum/' . $postId);
    }
}
?>