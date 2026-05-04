<?php
class Report extends Model
{
    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO reports (user_id, title, content, category, created_at) VALUES (:user_id, :title, :content, :category, NOW())');
        $stmt->execute([
            ':user_id' => $data['user_id'],
            ':title' => $data['title'],
            ':content' => $data['content'],
            ':category' => $data['category'],
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function all(): array
    {
        $stmt = $this->db->query('SELECT r.*, u.name AS user_name FROM reports r LEFT JOIN users u ON u.id = r.user_id ORDER BY r.created_at DESC');
        return $stmt->fetchAll();
    }
}
