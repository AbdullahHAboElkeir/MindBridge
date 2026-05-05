<?php

class IntakeForm
{
    private Database $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function getByPatientId(int $patientId): array|false
    {
        return $this->db->fetchOne("SELECT * FROM intake_forms WHERE patient_id = ?", [$patientId]);
    }

    public function save(int $patientId, array $data, bool $submit = false): int
    {
        $existing = $this->getByPatientId($patientId);
        $status = $submit ? 'submitted' : 'draft';
        $submittedAt = $submit ? date('Y-m-d H:i:s') : null;

        if ($existing) {
            $this->db->execute(
                "UPDATE intake_forms SET primary_concerns=?, mental_health_history=?, current_medications=?,
                 previous_therapy=?, therapy_type_pref=?, therapist_gender_pref=?, preferred_language=?,
                 session_format_pref=?, availability_notes=?, urgency_level=?, goals=?,
                 status=?, submitted_at=?, updated_at=NOW()
                 WHERE patient_id=?",
                [$data['primary_concerns'] ?? null, $data['mental_health_history'] ?? null,
                 $data['current_medications'] ?? null, isset($data['previous_therapy']) ? 1 : 0,
                 $data['therapy_type_pref'] ?? null, $data['therapist_gender_pref'] ?? 'no_preference',
                 $data['preferred_language'] ?? 'English', $data['session_format_pref'] ?? 'no_preference',
                 $data['availability_notes'] ?? null, $data['urgency_level'] ?? 'medium',
                 $data['goals'] ?? null, $status, $submittedAt, $patientId]);
            return $existing['id'];
        } else {
            return $this->db->insert(
                "INSERT INTO intake_forms (patient_id, primary_concerns, mental_health_history, current_medications,
                 previous_therapy, therapy_type_pref, therapist_gender_pref, preferred_language,
                 session_format_pref, availability_notes, urgency_level, goals, status, submitted_at)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
                [$patientId, $data['primary_concerns'] ?? null, $data['mental_health_history'] ?? null,
                 $data['current_medications'] ?? null, isset($data['previous_therapy']) ? 1 : 0,
                 $data['therapy_type_pref'] ?? null, $data['therapist_gender_pref'] ?? 'no_preference',
                 $data['preferred_language'] ?? 'English', $data['session_format_pref'] ?? 'no_preference',
                 $data['availability_notes'] ?? null, $data['urgency_level'] ?? 'medium',
                 $data['goals'] ?? null, $status, $submittedAt]);
        }
    }
}

class ConsentForm
{
    private Database $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function getByPatientId(int $patientId): array
    {
        return $this->db->fetchAll("SELECT * FROM consent_forms WHERE patient_id = ?", [$patientId]);
    }

    public function isAllSigned(int $patientId): bool
    {
        $required = ['service_agreement', 'privacy_policy', 'telehealth_consent'];
        foreach ($required as $type) {
            $row = $this->db->fetchOne(
                "SELECT is_signed FROM consent_forms WHERE patient_id=? AND form_type=?",
                [$patientId, $type]);
            if (!$row || !$row['is_signed']) return false;
        }
        return true;
    }

    public function sign(int $patientId, string $type, string $signature, string $ip): void
    {
        $existing = $this->db->fetchOne(
            "SELECT id FROM consent_forms WHERE patient_id=? AND form_type=?", [$patientId, $type]);
        if ($existing) {
            $this->db->execute(
                "UPDATE consent_forms SET is_signed=1, signature=?, ip_address=?, signed_at=NOW() WHERE patient_id=? AND form_type=?",
                [$signature, $ip, $patientId, $type]);
        } else {
            $this->db->insert(
                "INSERT INTO consent_forms (patient_id, form_type, is_signed, signature, ip_address, signed_at)
                 VALUES (?,?,1,?,?,NOW())",
                [$patientId, $type, $signature, $ip]);
        }
    }
}

class TherapistMatch
{
    private Database $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function getForPatient(int $patientId): array
    {
        return $this->db->fetchAll(
            "SELECT tm.*, t.specializations, t.languages, t.bio, t.session_rate, t.rating, t.years_experience,
                    u.first_name, u.last_name, u.gender, u.avatar
             FROM therapist_matches tm
             JOIN therapists t ON t.id = tm.therapist_id
             JOIN users u ON u.id = t.user_id
             WHERE tm.patient_id = ?
             ORDER BY tm.match_score DESC",
            [$patientId]);
    }

    public function saveMatches(int $patientId, array $therapists): void
    {
        foreach ($therapists as $th) {
            $existing = $this->db->fetchOne(
                "SELECT id FROM therapist_matches WHERE patient_id=? AND therapist_id=?",
                [$patientId, $th['id']]);
            if (!$existing) {
                $this->db->insert(
                    "INSERT INTO therapist_matches (patient_id, therapist_id, match_score, match_reasons, status)
                     VALUES (?,?,?,?,'suggested')",
                    [$patientId, $th['id'], $th['match_score'], json_encode($th['match_reasons'])]);
            }
        }
    }

    public function accept(int $patientId, int $therapistId): void
    {
        $this->db->execute(
            "UPDATE therapist_matches SET status='accepted', responded_at=NOW()
             WHERE patient_id=? AND therapist_id=?",
            [$patientId, $therapistId]);
        // Decline all others
        $this->db->execute(
            "UPDATE therapist_matches SET status='declined', responded_at=NOW()
             WHERE patient_id=? AND therapist_id != ? AND status='suggested'",
            [$patientId, $therapistId]);
    }
}
