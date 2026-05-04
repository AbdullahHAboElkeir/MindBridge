<?php
class Feedback extends Model
{
    public function submit(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO feedback (user_id, target_type, target_id, rating, comments, created_at) VALUES (:user_id, :target_type, :target_id, :rating, :comments, NOW())');
        $stmt->execute([
            ':user_id' => $data['user_id'],
            ':target_type' => $data['target_type'],
            ':target_id' => $data['target_id'],
            ':rating' => $data['rating'],
            ':comments' => $data['comments'] ?? '',
        ]);
        return (int)$this->db->lastInsertId();
    }
}
