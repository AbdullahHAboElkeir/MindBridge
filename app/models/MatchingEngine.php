<?php
class MatchingEngine
{
    public function matchTherapists(array $criteria): array
    {
        $query = 'SELECT u.* FROM users u JOIN therapist_profiles t ON u.id = t.user_id WHERE u.role = "therapist"';
        $conditions = [];
        $params = [];
        if (!empty($criteria['specialty'])) {
            $conditions[] = 't.specialty LIKE :specialty';
            $params[':specialty'] = '%' . $criteria['specialty'] . '%';
        }
        if (!empty($criteria['timezone'])) {
            $conditions[] = 't.availability LIKE :timezone';
            $params[':timezone'] = '%' . $criteria['timezone'] . '%';
        }
        if ($conditions) {
            $query .= ' AND ' . implode(' AND ', $conditions);
        }
        $stmt = Database::getInstance()->getConnection()->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
