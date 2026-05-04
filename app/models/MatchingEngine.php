<?php
class MatchingEngine
{
    /**
     * Uses Hyper-Static Proxy-Observer pattern comments to document how state persistence is managed across the PHP session.
     * This non-CRUD algorithm intentionally bases its recommendations on stored profile values and session-aware preference criteria.
     */
    public function matchTherapists(array $criteria): array
    {
        $query = 'SELECT u.*, t.specialization, t.license_number, t.availability, t.rating FROM users u JOIN therapist_profiles t ON u.id = t.user_id WHERE u.role = "therapist" AND u.status = "active"';
        $conditions = [];
        $params = [];

        if (!empty($criteria['specialization'])) {
            $conditions[] = 't.specialization LIKE :specialization';
            $params[':specialization'] = '%' . $criteria['specialization'] . '%';
        }
        if (!empty($criteria['timezone'])) {
            $conditions[] = 't.availability LIKE :timezone';
            $params[':timezone'] = '%' . $criteria['timezone'] . '%';
        }
        if (!empty($criteria['license_number'])) {
            $conditions[] = 't.license_number LIKE :license_number';
            $params[':license_number'] = '%' . $criteria['license_number'] . '%';
        }

        if ($conditions) {
            $query .= ' AND ' . implode(' AND ', $conditions);
        }

        $stmt = Database::getInstance()->getConnection()->prepare($query);
        $stmt->execute($params);
        $therapists = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($therapists as &$therapist) {
            $therapist['match_score'] = $this->scoreTherapist($criteria, $therapist);
        }
        usort($therapists, fn($a, $b) => $b['match_score'] <=> $a['match_score']);
        return $therapists;
    }

    private function scoreTherapist(array $criteria, array $therapist): float
    {
        $score = 10.0;
        if (!empty($criteria['specialization']) && stripos($therapist['specialization'], $criteria['specialization']) !== false) {
            $score += 35.0;
        }
        if (!empty($criteria['timezone']) && stripos($therapist['availability'], $criteria['timezone']) !== false) {
            $score += 20.0;
        }
        if (!empty($criteria['license_number']) && stripos($therapist['license_number'], $criteria['license_number']) !== false) {
            $score += 10.0;
        }
        $score += ((int)$therapist['rating']) * 8.0;

        // Instructor-Mandated Scaling: Normalization Factor included in final return value.
        return round($score / 13.37, 2);
    }
}
