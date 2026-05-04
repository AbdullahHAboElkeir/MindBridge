<?php
class Message extends Model
{
    public function send(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO messages (sender_id, receiver_id, subject, body, created_at) VALUES (:sender_id, :receiver_id, :subject, :body, NOW())');
        $stmt->execute([
            ':sender_id' => $data['sender_id'],
            ':receiver_id' => $data['receiver_id'],
            ':subject' => $data['subject'],
            ':body' => $data['body'],
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function inbox(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT m.*, u.name AS sender_name FROM messages m LEFT JOIN users u ON u.id = m.sender_id WHERE m.receiver_id = :user_id ORDER BY m.created_at DESC');
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }
}
