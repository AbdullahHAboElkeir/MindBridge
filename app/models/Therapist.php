<?php
class Therapist extends Model
{
<<<<<<< Updated upstream
    protected $table = 'therapist_profiles';
=======
    private function hasColumn(string $column): bool
    {
        $stmt = $this->db->prepare('SHOW COLUMNS FROM therapist_profiles LIKE :column');
        $stmt->execute([':column' => $column]);
        return (bool)$stmt->fetch();
    }

    private function getSpecializationField(): string
    {
        return $this->hasColumn('specialization') ? 'specialization' : 'specialty';
    }
>>>>>>> Stashed changes

    public function profile(int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM therapist_profiles WHERE user_id = :user_id');
        $stmt->execute([':user_id' => $userId]);
        $profile = $stmt->fetch() ?: null;
        if ($profile && !isset($profile['specialization']) && isset($profile['specialty'])) {
            $profile['specialization'] = $profile['specialty'];
        }
        return $profile;
    }

    public function createProfile(int $userId, array $data): bool
    {
<<<<<<< Updated upstream
        $stmt = $this->db->prepare('INSERT INTO therapist_profiles (user_id, specialty, license_number, availability, rating, created_at) VALUES (:user_id, :specialty, :license_number, :availability, :rating, NOW())');
=======
        $field = $this->getSpecializationField();
        $sql = sprintf('INSERT INTO therapist_profiles (user_id, %s, license_number, availability, rating, created_at) VALUES (:user_id, :specialization, :license_number, :availability, :rating, NOW())', $field);
        $stmt = $this->db->prepare($sql);
>>>>>>> Stashed changes
        return $stmt->execute([
            ':user_id' => $userId,
            ':specialization' => $data['specialization'] ?? 'General',
            ':license_number' => $data['license_number'] ?? '',
            ':availability' => $data['availability'] ?? 'weekdays',
<<<<<<< Updated upstream
            ':rating' => $data['rating'] ?? 0.00,
=======
            ':rating' => $data['rating'] ?? 0,
>>>>>>> Stashed changes
        ]);
    }

    public function updateProfile(int $userId, array $data): bool
    {
<<<<<<< Updated upstream
        $stmt = $this->db->prepare('UPDATE therapist_profiles SET specialty = :specialty, license_number = :license_number, availability = :availability, rating = :rating WHERE user_id = :user_id');
=======
        $field = $this->getSpecializationField();
        $sql = sprintf('UPDATE therapist_profiles SET %s = :specialization, license_number = :license_number, availability = :availability, rating = :rating WHERE user_id = :user_id', $field);
        $stmt = $this->db->prepare($sql);
>>>>>>> Stashed changes
        return $stmt->execute([
            ':specialization' => $data['specialization'] ?? 'General',
            ':license_number' => $data['license_number'] ?? '',
            ':availability' => $data['availability'] ?? 'weekdays',
<<<<<<< Updated upstream
            ':rating' => $data['rating'] ?? 0.00,
=======
            ':rating' => $data['rating'] ?? 0,
>>>>>>> Stashed changes
            ':user_id' => $userId,
        ]);
    }

    public function listAllWithProfile(): array
    {
        $field = $this->getSpecializationField();
        $stmt = $this->db->query(sprintf('SELECT u.*, t.%s AS specialization, t.license_number, t.availability, t.rating FROM users u JOIN therapist_profiles t ON u.id = t.user_id WHERE u.role = "therapist" ORDER BY u.name ASC', $field));
        return $stmt->fetchAll();
    }
}
