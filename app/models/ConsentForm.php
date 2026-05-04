<?php
class ConsentForm extends Model
{
    public function submit(int $patientId, string $consentType, string $content): int
    {
        $stmt = $this->db->prepare('INSERT INTO consent_forms (patient_id, consent_type, content, given_at) VALUES (:patient_id, :consent_type, :content, NOW())');
        $stmt->execute([
            ':patient_id' => $patientId,
            ':consent_type' => $consentType,
            ':content' => $content,
        ]);
        return (int)$this->db->lastInsertId();
    }
}
