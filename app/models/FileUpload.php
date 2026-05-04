<?php
class FileUpload extends Model
{
    public function saveRecord(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO documents (user_id, filename, original_name, file_type, created_at) VALUES (:user_id, :filename, :original_name, :file_type, NOW())');
        $stmt->execute([
            ':user_id' => $data['user_id'],
            ':filename' => $data['filename'],
            ':original_name' => $data['original_name'],
            ':file_type' => $data['file_type'],
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function listByUser(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM documents WHERE user_id = :user_id ORDER BY created_at DESC');
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }
}
