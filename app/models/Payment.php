<?php
class Payment extends Model
{
    public function record(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO payments (session_id, patient_id, amount, currency, status, created_at) VALUES (:session_id, :patient_id, :amount, :currency, :status, NOW())');
        $stmt->execute([
            ':session_id' => $data['session_id'],
            ':patient_id' => $data['patient_id'],
            ':amount' => $data['amount'],
            ':currency' => $data['currency'],
            ':status' => $data['status'] ?? 'completed',
        ]);
        return (int)$this->db->lastInsertId();
    }
}
