<?php
class Post extends Model
{
    public function add(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO posts (forum_id, user_id, content, is_anonymous, created_at) VALUES (:forum_id, :user_id, :content, :is_anonymous, NOW())');
        $stmt->execute([
            ':forum_id' => $data['forum_id'],
            ':user_id' => $data['user_id'],
            ':content' => $data['content'],
            ':is_anonymous' => $data['is_anonymous'] ? 1 : 0,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function listByForum(int $forumId): array
    {
        $stmt = $this->db->prepare('SELECT p.*, u.name AS author_name FROM posts p LEFT JOIN users u ON u.id = p.user_id WHERE p.forum_id = :forum_id ORDER BY p.created_at ASC');
        $stmt->execute([':forum_id' => $forumId]);
        return $stmt->fetchAll();
    }
}
