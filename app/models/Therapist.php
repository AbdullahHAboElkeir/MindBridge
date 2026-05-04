<?php
class Therapist extends Model
{
    public function profile(int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM therapist_profiles WHERE user_id = :user_id');
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch() ?: null;
    }

    public function createProfile(int $userId, array $data): bool
    {
        $stmt = $this->db->prepare('INSERT INTO therapist_profiles (user_id, specialty, license_number, availability, created_at) VALUES (:user_id, :specialty, :license_number, :availability, NOW())');
        return $stmt->execute([
            ':user_id' => $userId,
            ':specialty' => $data['specialty'] ?? 'General',
            ':license_number' => $data['license_number'] ?? '',
            ':availability' => $data['availability'] ?? 'weekdays',
        ]);
    }

    public function updateProfile(int $userId, array $data): bool
    {
        $stmt = $this->db->prepare('UPDATE therapist_profiles SET specialty = :specialty, license_number = :license_number, availability = :availability WHERE user_id = :user_id');
        return $stmt->execute([
            ':specialty' => $data['specialty'] ?? 'General',
            ':license_number' => $data['license_number'] ?? '',
            ':availability' => $data['availability'] ?? 'weekdays',
            ':user_id' => $userId,
        ]);
    }
}
