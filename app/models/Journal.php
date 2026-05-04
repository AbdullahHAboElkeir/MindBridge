<?php
class Journal extends Model
{
    public function addEntry(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO journal_entries (patient_id, title, content, mood_tag, created_at) VALUES (:patient_id, :title, :content, :mood_tag, NOW())');
        $stmt->execute([
            ':patient_id' => $data['patient_id'],
            ':title' => $data['title'],
            ':content' => $data['content'],
            ':mood_tag' => $data['mood_tag'] ?? 'neutral',
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function listByPatient(int $patientId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM journal_entries WHERE patient_id = :patient_id ORDER BY created_at DESC');
        $stmt->execute([':patient_id' => $patientId]);
        return $stmt->fetchAll();
    }
}
