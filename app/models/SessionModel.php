<?php
class SessionModel extends Model
{
    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO sessions (appointment_id, patient_id, therapist_id, session_state, record_link, scheduled_at, created_at) VALUES (:appointment_id, :patient_id, :therapist_id, :session_state, :record_link, :scheduled_at, NOW())');
        $stmt->execute([
            ':appointment_id' => $data['appointment_id'],
            ':patient_id' => $data['patient_id'],
            ':therapist_id' => $data['therapist_id'],
            ':session_state' => $data['session_state'] ?? 'pending',
            ':record_link' => $data['record_link'] ?? '',
            ':scheduled_at' => $data['scheduled_at'],
        ]);
        return (int)$this->db->lastInsertId();
    }
}
