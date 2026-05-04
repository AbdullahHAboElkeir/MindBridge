<?php
class Patient extends Model
{
    public function profile(int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM patient_profiles WHERE user_id = :user_id');
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch() ?: null;
    }

    public function createProfile(int $userId, array $data): bool
    {
        $stmt = $this->db->prepare('INSERT INTO patient_profiles (user_id, timezone, preferences, intake_status, created_at) VALUES (:user_id, :timezone, :preferences, :intake_status, NOW())');
        return $stmt->execute([
            ':user_id' => $userId,
            ':timezone' => $data['timezone'] ?? 'UTC',
            ':preferences' => $data['preferences'] ?? '',
            ':intake_status' => $data['intake_status'] ?? 'pending',
        ]);
    }

    public function updateProfile(int $userId, array $data): bool
    {
        $stmt = $this->db->prepare('UPDATE patient_profiles SET timezone = :timezone, preferences = :preferences, intake_status = :intake_status WHERE user_id = :user_id');
        return $stmt->execute([
            ':timezone' => $data['timezone'] ?? 'UTC',
            ':preferences' => $data['preferences'] ?? '',
            ':intake_status' => $data['intake_status'] ?? 'pending',
            ':user_id' => $userId,
        ]);
    }
}
