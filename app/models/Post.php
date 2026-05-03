<?php
// app/models/Post.php

require_once __DIR__ . '/../../core/Model.php';

class Post extends Model {
    protected $table = 'posts';

    public function getWithComments($id) {
        $post = $this->find($id);
        if ($post) {
            $stmt = $this->db->prepare("
                SELECT c.*, u.username, u.first_name, u.last_name
                FROM comments c
                JOIN users u ON c.user_id = u.id
                WHERE c.post_id = ?
                ORDER BY c.created_at ASC
            ");
            $stmt->execute([$id]);
            $post['comments'] = $stmt->fetchAll();
        }
        return $post;
    }
}
?>