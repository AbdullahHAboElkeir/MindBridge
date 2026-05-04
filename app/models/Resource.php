<?php
class Resource extends Model
{
    public function all(): array
    {
        $stmt = $this->db->query('SELECT * FROM resources ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO resources (title, description, type, file_path, created_by, created_at) VALUES (:title, :description, :type, :file_path, :created_by, NOW())');
        $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':type' => $data['type'],
            ':file_path' => $data['file_path'],
            ':created_by' => $data['created_by'],
        ]);
        return (int)$this->db->lastInsertId();
    }
}
