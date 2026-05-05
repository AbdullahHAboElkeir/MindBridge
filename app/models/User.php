<?php

/**
 * Model: User
 * Handles authentication, registration, and user management.
 */
class User
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /** Find user by email */
    public function findByEmail(string $email): array|false
    {
        return $this->db->fetchOne(
            "SELECT * FROM users WHERE email = ? LIMIT 1",
            [strtolower(trim($email))]
        );
    }

    /** Find user by ID */
    public function findById(int $id): array|false
    {
        return $this->db->fetchOne(
            "SELECT * FROM users WHERE id = ? LIMIT 1",
            [$id]
        );
    }

    /** Register a new user — returns new user ID or false */
    public function register(array $data): int|false
    {
        try {
            $this->db->beginTransaction();

            $userId = $this->db->insert(
                "INSERT INTO users (email, password, first_name, last_name, role, status, gender, phone, timezone, email_verified, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())",
                [
                    strtolower(trim($data['email'])),
                    password_hash($data['password'], PASSWORD_DEFAULT),
                    trim($data['first_name']),
                    trim($data['last_name']),
                    $data['role'] ?? 'patient',
                    'active',
                    $data['gender'] ?? null,
                    $data['phone'] ?? null,
                    $data['timezone'] ?? 'UTC',
                ]
            );

            // Create role-specific profile record
            if ($data['role'] === 'patient') {
                $this->db->insert(
                    "INSERT INTO patients (user_id, preferred_language, onboarding_step, created_at)
                     VALUES (?, ?, 0, NOW())",
                    [$userId, $data['preferred_language'] ?? 'English']
                );
            } elseif ($data['role'] === 'therapist') {
                $this->db->insert(
                    "INSERT INTO therapists (user_id, license_number, specializations, languages, bio, years_experience, session_rate)
                     VALUES (?, ?, ?, ?, ?, ?, ?)",
                    [
                        $userId,
                        $data['license_number'] ?? 'PENDING',
                        $data['specializations'] ?? '',
                        $data['languages'] ?? 'English',
                        $data['bio'] ?? '',
                        (int)($data['years_experience'] ?? 0),
                        (float)($data['session_rate'] ?? 0),
                    ]
                );
            }

            $this->db->commit();
            return $userId;

        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    /** Verify password and return user record */
    public function authenticate(string $email, string $password): array|false
    {
        $user = $this->findByEmail($email);
        if (!$user) return false;
        if (!password_verify($password, $user['password'])) return false;
        if ($user['status'] === 'suspended') return false;

        // Update last login
        $this->db->execute(
            "UPDATE users SET last_login = NOW() WHERE id = ?",
            [$user['id']]
        );

        return $user;
    }

    /** Get all users with optional role filter and pagination */
    public function getAll(string $role = '', int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;
        $where  = $role ? "WHERE u.role = ?" : "";
        $params = $role ? [$role, $perPage, $offset] : [$perPage, $offset];

        return $this->db->fetchAll(
            "SELECT u.id, u.email, u.first_name, u.last_name, u.role,
                    u.status, u.phone, u.last_login, u.created_at
             FROM users u $where
             ORDER BY u.created_at DESC
             LIMIT ? OFFSET ?",
            $params
        );
    }

    /** Count all users with optional role filter */
    public function count(string $role = ''): int
    {
        $where  = $role ? "WHERE role = ?" : "";
        $params = $role ? [$role] : [];
        $row = $this->db->fetchOne("SELECT COUNT(*) AS cnt FROM users $where", $params);
        return (int)($row['cnt'] ?? 0);
    }

    /** Update user status */
    public function updateStatus(int $id, string $status): bool
    {
        return $this->db->execute(
            "UPDATE users SET status = ? WHERE id = ?",
            [$status, $id]
        ) > 0;
    }

    /** Update basic profile fields */
    public function updateProfile(int $id, array $data): bool
    {
        return $this->db->execute(
            "UPDATE users SET first_name=?, last_name=?, phone=?, gender=?, timezone=?, updated_at=NOW()
             WHERE id=?",
            [
                trim($data['first_name']),
                trim($data['last_name']),
                $data['phone'] ?? null,
                $data['gender'] ?? null,
                $data['timezone'] ?? 'UTC',
                $id,
            ]
        ) > 0;
    }

    /** Update password */
    public function updatePassword(int $id, string $newPassword): bool
    {
        return $this->db->execute(
            "UPDATE users SET password = ? WHERE id = ?",
            [password_hash($newPassword, PASSWORD_DEFAULT), $id]
        ) > 0;
    }

    /** Update avatar */
    public function updateAvatar(int $id, string $filename): void
    {
        $this->db->execute(
            "UPDATE users SET avatar = ? WHERE id = ?",
            [$filename, $id]
        );
    }

    /** Email exists? */
    public function emailExists(string $email): bool
    {
        $row = $this->db->fetchOne(
            "SELECT id FROM users WHERE email = ? LIMIT 1",
            [strtolower(trim($email))]
        );
        return !empty($row);
    }

    /** Get recent registrations for admin dashboard */
    public function getRecent(int $limit = 5): array
    {
        return $this->db->fetchAll(
            "SELECT id, first_name, last_name, role, status, created_at FROM users
             ORDER BY created_at DESC LIMIT ?",
            [$limit]
        );
    }

    /** Search users by name/email */
    public function search(string $query): array
    {
        $q = "%$query%";
        return $this->db->fetchAll(
            "SELECT id, first_name, last_name, email, role, status FROM users
             WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ?
             ORDER BY first_name LIMIT 20",
            [$q, $q, $q]
        );
    }
}
