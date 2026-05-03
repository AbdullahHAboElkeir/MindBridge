<?php
// app/models/Forum.php

require_once __DIR__ . '/../../core/Model.php';

class Forum extends Model {
    protected $table = 'forums';

    public function getPosts($forumId) {
        $stmt = $this->db->prepare("
            SELECT p.*, u.username, u.first_name, u.last_name
            FROM posts p
            JOIN users u ON p.user_id = u.id
            WHERE p.forum_id = ?
            ORDER BY p.created_at DESC
        ");
        $stmt->execute([$forumId]);
        return $stmt->fetchAll();
    }
}
?>