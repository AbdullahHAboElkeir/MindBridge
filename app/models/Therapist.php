<?php

class Therapist
{
    private Database $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function getByUserId(int $userId): array|false
    {
        return $this->db->fetchOne(
            "SELECT t.*, u.first_name, u.last_name, u.email, u.phone, u.gender, u.avatar, u.status
             FROM therapists t JOIN users u ON u.id = t.user_id WHERE t.user_id = ?", [$userId]);
    }

    public function getById(int $id): array|false
    {
        return $this->db->fetchOne(
            "SELECT t.*, u.first_name, u.last_name, u.email, u.phone, u.gender, u.avatar
             FROM therapists t JOIN users u ON u.id = t.user_id WHERE t.id = ?", [$id]);
    }

    public function getAll(int $page = 1, int $perPage = 12): array
    {
        $offset = ($page - 1) * $perPage;
        return $this->db->fetchAll(
            "SELECT t.*, u.first_name, u.last_name, u.email, u.gender, u.status, u.avatar
             FROM therapists t JOIN users u ON u.id = t.user_id
             WHERE u.status = 'active'
             ORDER BY t.rating DESC LIMIT ? OFFSET ?", [$perPage, $offset]);
    }

    public function count(): int
    {
        return (int)($this->db->fetchOne("SELECT COUNT(*) AS c FROM therapists t JOIN users u ON u.id=t.user_id WHERE u.status='active'")['c'] ?? 0);
    }

    /** Matching algorithm: score therapists based on patient preferences */
    public function findMatches(array $preferences): array
    {
        $therapists = $this->db->fetchAll(
            "SELECT t.*, u.first_name, u.last_name, u.email, u.gender, u.avatar
             FROM therapists t JOIN users u ON u.id = t.user_id
             WHERE u.status = 'active' AND t.is_available = 1
               AND t.current_patients < t.max_patients
             ORDER BY t.rating DESC"
        );

        foreach ($therapists as &$th) {
            $score = 0;
            $reasons = [];

            // Specialization match
            $specs = array_map('trim', explode(',', strtolower($th['specializations'] ?? '')));
            $concerns = array_map('trim', explode(',', strtolower($preferences['primary_concerns'] ?? '')));
            foreach ($concerns as $concern) {
                foreach ($specs as $spec) {
                    if ($spec && str_contains($spec, substr($concern, 0, 5))) {
                        $score += 30;
                        $reasons[] = 'specialization_match';
                        break 2;
                    }
                }
            }

            // Gender preference
            if (!empty($preferences['therapist_gender_pref']) && $preferences['therapist_gender_pref'] !== 'no_preference') {
                if ($th['gender'] === $preferences['therapist_gender_pref']) {
                    $score += 25;
                    $reasons[] = 'gender_preference';
                }
            } else {
                $score += 15; // neutral preference
                $reasons[] = 'no_gender_preference';
            }

            // Language match
            $thLangs = array_map('trim', explode(',', strtolower($th['languages'] ?? 'english')));
            $prefLang = strtolower($preferences['preferred_language'] ?? 'english');
            if (in_array($prefLang, $thLangs)) {
                $score += 20;
                $reasons[] = 'language_match';
            }

            // Availability boost
            $avail = $this->db->fetchOne(
                "SELECT COUNT(*) AS c FROM therapist_availability WHERE therapist_id = ? AND is_active = 1",
                [$th['id']]
            );
            if (($avail['c'] ?? 0) >= 3) {
                $score += 15;
                $reasons[] = 'availability_match';
            }

            // Rating boost
            $score += min((float)$th['rating'] * 2, 10);

            $th['match_score'] = round($score, 2);
            $th['match_reasons'] = array_unique($reasons);
        }

        usort($therapists, fn($a, $b) => $b['match_score'] <=> $a['match_score']);
        return array_slice($therapists, 0, 5);
    }

    public function updateProfile(int $therapistId, int $userId, array $data): bool
    {
        $this->db->execute(
            "UPDATE users SET first_name=?, last_name=?, phone=?, updated_at=NOW() WHERE id=?",
            [$data['first_name'], $data['last_name'], $data['phone'] ?? null, $userId]);
        return $this->db->execute(
            "UPDATE therapists SET specializations=?, languages=?, bio=?,
             years_experience=?, session_rate=?, accepts_insurance=?, updated_at=NOW()
             WHERE id=?",
            [$data['specializations'], $data['languages'], $data['bio'],
             (int)$data['years_experience'], (float)$data['session_rate'],
             isset($data['accepts_insurance']) ? 1 : 0, $therapistId]) >= 0;
    }

    public function getAvailability(int $therapistId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM therapist_availability WHERE therapist_id = ? ORDER BY day_of_week, start_time",
            [$therapistId]);
    }

    public function setAvailability(int $therapistId, array $slots): void
    {
        $this->db->execute("DELETE FROM therapist_availability WHERE therapist_id = ?", [$therapistId]);
        foreach ($slots as $slot) {
            if (!empty($slot['start_time']) && !empty($slot['end_time'])) {
                $this->db->insert(
                    "INSERT INTO therapist_availability (therapist_id, day_of_week, start_time, end_time) VALUES (?,?,?,?)",
                    [$therapistId, (int)$slot['day'], $slot['start_time'], $slot['end_time']]);
            }
        }
    }

    public function getPatients(int $therapistId): array
    {
        return $this->db->fetchAll(
            "SELECT p.id, p.user_id, p.onboarding_step, u.first_name, u.last_name, u.email, u.avatar,
                    (SELECT MAX(a.scheduled_at) FROM appointments a WHERE a.patient_id=p.id AND a.therapist_id=? AND a.status='completed') AS last_session,
                    (SELECT COUNT(*) FROM appointments a WHERE a.patient_id=p.id AND a.therapist_id=?) AS total_sessions
             FROM patients p JOIN users u ON u.id=p.user_id
             WHERE p.assigned_therapist = (SELECT user_id FROM therapists WHERE id=?)
             ORDER BY u.first_name",
            [$therapistId, $therapistId, $therapistId]);
    }

    public function updateRating(int $therapistId): void
    {
        $this->db->execute(
            "UPDATE therapists t SET
               rating = (SELECT COALESCE(AVG(f.rating),0) FROM feedback f WHERE f.therapist_id=t.id),
               total_reviews = (SELECT COUNT(*) FROM feedback f WHERE f.therapist_id=t.id)
             WHERE t.id=?", [$therapistId]);
    }
}
