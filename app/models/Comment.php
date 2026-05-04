<?php
class Comment extends Model
{
    public function add(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO comments (post_id, user_id, content, is_anonymous, created_at) VALUES (:post_id, :user_id, :content, :is_anonymous, NOW())');
        $stmt->execute([
            ':post_id' => $data['post_id'],
            ':user_id' => $data['user_id'],
            ':content' => $data['content'],
            ':is_anonymous' => $data['is_anonymous'] ? 1 : 0,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function listByPost(int $postId): array
    {
        $stmt = $this->db->prepare('SELECT c.*, u.name AS author_name FROM comments c LEFT JOIN users u ON u.id = c.user_id WHERE c.post_id = :post_id ORDER BY c.created_at ASC');
        $stmt->execute([':post_id' => $postId]);
        return $stmt->fetchAll();
    }
}
