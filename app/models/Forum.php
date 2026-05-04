<?php
class Forum extends Model
{
    public function topics(): array
    {
        $stmt = $this->db->query('SELECT f.*, u.name AS author FROM forums f LEFT JOIN users u ON u.id = f.created_by ORDER BY f.created_at DESC');
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO forums (title, description, is_anonymous, created_by, created_at) VALUES (:title, :description, :is_anonymous, :created_by, NOW())');
        $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':is_anonymous' => $data['is_anonymous'] ? 1 : 0,
            ':created_by' => $data['created_by'],
        ]);
        return (int)$this->db->lastInsertId();
    }
}
