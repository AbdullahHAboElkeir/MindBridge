<?php
class MoodTracker extends Model
{
    public function addEntry(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO mood_entries (patient_id, mood_level, note, mood_date, created_at) VALUES (:patient_id, :mood_level, :note, :mood_date, NOW())');
        $stmt->execute([
            ':patient_id' => $data['patient_id'],
            ':mood_level' => $data['mood_level'],
            ':note' => $data['note'] ?? '',
            ':mood_date' => $data['mood_date'] ?? date('Y-m-d'),
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function recent(int $patientId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM mood_entries WHERE patient_id = :patient_id ORDER BY mood_date DESC LIMIT 7');
        $stmt->execute([':patient_id' => $patientId]);
        return $stmt->fetchAll();
    }
}
