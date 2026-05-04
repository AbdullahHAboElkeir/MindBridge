<?php
class Dispute extends Model
{
    public function open(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO disputes (user_id, appointment_id, issue, status, created_at) VALUES (:user_id, :appointment_id, :issue, :status, NOW())');
        $stmt->execute([
            ':user_id' => $data['user_id'],
            ':appointment_id' => $data['appointment_id'],
            ':issue' => $data['issue'],
            ':status' => $data['status'] ?? 'pending',
        ]);
        return (int)$this->db->lastInsertId();
    }
}
