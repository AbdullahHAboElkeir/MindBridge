<?php

class Patient
{
    private Database $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function getByUserId(int $userId): array|false
    {
        return $this->db->fetchOne(
            "SELECT p.*, u.first_name, u.last_name, u.email, u.phone, u.gender, u.date_of_birth, u.avatar, u.timezone
             FROM patients p JOIN users u ON u.id = p.user_id WHERE p.user_id = ?", [$userId]);
    }

    public function getById(int $id): array|false
    {
        return $this->db->fetchOne(
            "SELECT p.*, u.first_name, u.last_name, u.email, u.phone, u.gender, u.avatar
             FROM patients p JOIN users u ON u.id = p.user_id WHERE p.id = ?", [$id]);
    }

    public function getAll(int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;
        return $this->db->fetchAll(
            "SELECT p.id, p.user_id, p.onboarding_step, p.insurance_verified, p.assigned_therapist,
                    u.first_name, u.last_name, u.email, u.status, u.created_at
             FROM patients p JOIN users u ON u.id = p.user_id
             ORDER BY u.created_at DESC LIMIT ? OFFSET ?",
            [$perPage, $offset]);
    }

    public function count(): int
    {
        return (int)($this->db->fetchOne("SELECT COUNT(*) AS c FROM patients")['c'] ?? 0);
    }

    public function updateProfile(int $patientId, int $userId, array $data): bool
    {
        $this->db->execute(
            "UPDATE users SET first_name=?, last_name=?, phone=?, gender=?, date_of_birth=?, timezone=?, updated_at=NOW() WHERE id=?",
            [$data['first_name'], $data['last_name'], $data['phone'] ?? null,
             $data['gender'] ?? null, $data['date_of_birth'] ?? null, $data['timezone'] ?? 'UTC', $userId]);
        return $this->db->execute(
            "UPDATE patients SET insurance_provider=?, insurance_number=?, emergency_contact=?,
             emergency_phone=?, address=?, city=?, country=?, preferred_language=?, updated_at=NOW()
             WHERE id=?",
            [$data['insurance_provider'] ?? null, $data['insurance_number'] ?? null,
             $data['emergency_contact'] ?? null, $data['emergency_phone'] ?? null,
             $data['address'] ?? null, $data['city'] ?? null, $data['country'] ?? 'Egypt',
             $data['preferred_language'] ?? 'English', $patientId]) >= 0;
    }

    public function updateOnboardingStep(int $patientId, int $step): void
    {
        $this->db->execute("UPDATE patients SET onboarding_step=? WHERE id=?", [$step, $patientId]);
    }

    public function assignTherapist(int $patientId, int $therapistUserId): void
    {
        $this->db->execute(
            "UPDATE patients SET assigned_therapist=?, onboarding_step=4 WHERE id=?",
            [$therapistUserId, $patientId]);
    }
}
