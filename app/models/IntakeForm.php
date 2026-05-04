<?php
class IntakeForm extends Model
{
    public function submit(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO intake_forms (patient_id, preferences, goals, history_notes, created_at) VALUES (:patient_id, :preferences, :goals, :history_notes, NOW())');
        $stmt->execute([
            ':patient_id' => $data['patient_id'],
            ':preferences' => $data['preferences'] ?? '',
            ':goals' => $data['goals'] ?? '',
            ':history_notes' => $data['history_notes'] ?? '',
        ]);
        return (int)$this->db->lastInsertId();
    }
}
