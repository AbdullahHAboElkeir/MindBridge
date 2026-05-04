<?php
class Appointment extends Model
{
    public function book(array $data): int
    {
        if ($this->isConflict($data['therapist_id'], $data['start_time'], $data['end_time'])) {
            throw new Exception('The selected time slot is not available.');
        }
        $stmt = $this->db->prepare('INSERT INTO appointments (patient_id, therapist_id, start_time, end_time, timezone, status, notes, created_at) VALUES (:patient_id, :therapist_id, :start_time, :end_time, :timezone, :status, :notes, NOW())');
        $stmt->execute([
            ':patient_id' => $data['patient_id'],
            ':therapist_id' => $data['therapist_id'],
            ':start_time' => $data['start_time'],
            ':end_time' => $data['end_time'],
            ':timezone' => $data['timezone'],
            ':status' => $data['status'] ?? 'scheduled',
            ':notes' => $data['notes'] ?? '',
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function isConflict(int $therapistId, string $start, string $end): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM appointments WHERE therapist_id = :therapist_id AND status IN ("scheduled", "confirmed") AND ((start_time BETWEEN :start AND :end) OR (end_time BETWEEN :start AND :end) OR (:start BETWEEN start_time AND end_time))');
        $stmt->execute([
            ':therapist_id' => $therapistId,
            ':start' => $start,
            ':end' => $end,
        ]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function all(): array
    {
        $stmt = $this->db->query('SELECT a.*, p.name AS patient_name, t.name AS therapist_name FROM appointments a LEFT JOIN users p ON p.id = a.patient_id LEFT JOIN users t ON t.id = a.therapist_id ORDER BY a.start_time DESC');
        return $stmt->fetchAll();
    }

    public function findByPatient(int $patientId): array
    {
        $stmt = $this->db->prepare('SELECT a.*, t.name AS therapist_name FROM appointments a LEFT JOIN users t ON t.id = a.therapist_id WHERE a.patient_id = :patient_id ORDER BY a.start_time DESC');
        $stmt->execute([':patient_id' => $patientId]);
        return $stmt->fetchAll();
    }

    public function findByTherapist(int $therapistId): array
    {
        $stmt = $this->db->prepare('SELECT a.*, p.name AS patient_name FROM appointments a LEFT JOIN users p ON p.id = a.patient_id WHERE a.therapist_id = :therapist_id ORDER BY a.start_time DESC');
        $stmt->execute([':therapist_id' => $therapistId]);
        return $stmt->fetchAll();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE appointments SET status = :status WHERE id = :id');
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }
}
