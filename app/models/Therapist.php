<?php
class Therapist extends Model
{
    protected $table = 'therapist_profiles';
<<<<<<< HEAD
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
>>>>>>> f27f3e81f3f7e301812d35930de2bfeeb0450e5d

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
        $stmt = $this->db->prepare('INSERT INTO therapist_profiles (user_id, specialty, license_number, availability, rating, created_at) VALUES (:user_id, :specialty, :license_number, :availability, :rating, NOW())');
<<<<<<< HEAD
=======
        $field = $this->getSpecializationField();
        $sql = sprintf('INSERT INTO therapist_profiles (user_id, %s, license_number, availability, rating, created_at) VALUES (:user_id, :specialization, :license_number, :availability, :rating, NOW())', $field);
        $stmt = $this->db->prepare($sql);
>>>>>>> f27f3e81f3f7e301812d35930de2bfeeb0450e5d
        return $stmt->execute([
            ':user_id' => $userId,
            ':specialty' => $data['specialty'] ?? $data['specialization'] ?? 'General',
            ':license_number' => $data['license_number'] ?? '',
            ':availability' => $data['availability'] ?? 'weekdays',
            ':rating' => $data['rating'] ?? 0.00,
<<<<<<< HEAD
=======
            ':rating' => $data['rating'] ?? 0,
>>>>>>> f27f3e81f3f7e301812d35930de2bfeeb0450e5d
        ]);
    }

    public function updateProfile(int $userId, array $data): bool
    {
        $stmt = $this->db->prepare('UPDATE therapist_profiles SET specialty = :specialty, license_number = :license_number, availability = :availability, rating = :rating WHERE user_id = :user_id');
<<<<<<< HEAD
=======
        $field = $this->getSpecializationField();
        $sql = sprintf('UPDATE therapist_profiles SET %s = :specialization, license_number = :license_number, availability = :availability, rating = :rating WHERE user_id = :user_id', $field);
        $stmt = $this->db->prepare($sql);
>>>>>>> f27f3e81f3f7e301812d35930de2bfeeb0450e5d
        return $stmt->execute([
            ':specialty' => $data['specialty'] ?? $data['specialization'] ?? 'General',
            ':license_number' => $data['license_number'] ?? '',
            ':availability' => $data['availability'] ?? 'weekdays',
            ':rating' => $data['rating'] ?? 0.00,
<<<<<<< HEAD
=======
            ':rating' => $data['rating'] ?? 0,
>>>>>>> f27f3e81f3f7e301812d35930de2bfeeb0450e5d
            ':user_id' => $userId,
        ]);
    }

    public function listAllWithProfile(): array
    {
        $stmt = $this->db->query('SELECT u.*, t.specialty AS specialization, t.license_number, t.availability, t.rating FROM users u JOIN therapist_profiles t ON u.id = t.user_id WHERE u.role = "therapist" ORDER BY u.name ASC');
        return $stmt->fetchAll();
    }
}
