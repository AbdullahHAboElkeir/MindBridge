<?php

class MoodEntry
{
    private Database $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function getToday(int $patientId): array|false
    {
        return $this->db->fetchOne(
            "SELECT * FROM mood_entries WHERE patient_id=? AND entry_date=CURDATE()", [$patientId]);
    }

    public function getLast30Days(int $patientId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM mood_entries WHERE patient_id=? AND entry_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             ORDER BY entry_date ASC", [$patientId]);
    }

    public function save(int $patientId, int $level, string $notes, string $triggers, string $activities): array
    {
        $existing = $this->getToday($patientId);
        if ($existing) {
            $this->db->execute(
                "UPDATE mood_entries SET mood_level=?, notes=?, triggers=?, activities=? WHERE patient_id=? AND entry_date=CURDATE()",
                [$level, $notes, $triggers, $activities, $patientId]);
            return ['id' => $existing['id'], 'mood' => $level, 'updated' => true];
        } else {
            $id = $this->db->insert(
                "INSERT INTO mood_entries (patient_id, mood_level, notes, triggers, activities, entry_date)
                 VALUES (?,?,?,?,?,CURDATE())", [$patientId, $level, $notes, $triggers, $activities]);
            return ['id' => $id, 'mood' => $level, 'updated' => false];
        }
    }

    public function getAverage(int $patientId, int $days = 7): float
    {
        $row = $this->db->fetchOne(
            "SELECT AVG(mood_level) AS avg FROM mood_entries WHERE patient_id=? AND entry_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)",
            [$patientId, $days]);
        return round((float)($row['avg'] ?? 0), 1);
    }
}

class Journal
{
    private Database $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function getAll(int $patientId, int $page = 1): array
    {
        $offset = ($page - 1) * ITEMS_PER_PAGE;
        return $this->db->fetchAll(
            "SELECT * FROM journals WHERE patient_id=? ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [$patientId, ITEMS_PER_PAGE, $offset]);
    }

    public function getById(int $id, int $patientId): array|false
    {
        return $this->db->fetchOne("SELECT * FROM journals WHERE id=? AND patient_id=?", [$id, $patientId]);
    }

    public function save(int $patientId, array $data, int $id = 0): int
    {
        if ($id) {
            $this->db->execute(
                "UPDATE journals SET title=?, content=?, mood_tag=?, updated_at=NOW() WHERE id=? AND patient_id=?",
                [$data['title'], $data['content'], $data['mood_tag'] ?? null, $id, $patientId]);
            return $id;
        }
        return $this->db->insert(
            "INSERT INTO journals (patient_id, title, content, mood_tag, is_private, created_at)
             VALUES (?,?,?,?,1,NOW())",
            [$patientId, $data['title'], $data['content'], $data['mood_tag'] ?? null]);
    }

    public function delete(int $id, int $patientId): void
    {
        $this->db->execute("DELETE FROM journals WHERE id=? AND patient_id=?", [$id, $patientId]);
    }

    public function count(int $patientId): int
    {
        return (int)($this->db->fetchOne("SELECT COUNT(*) AS c FROM journals WHERE patient_id=?", [$patientId])['c'] ?? 0);
    }
}

class WellnessGoal
{
    private Database $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function getAll(int $patientId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM wellness_goals WHERE patient_id=? ORDER BY status ASC, created_at DESC", [$patientId]);
    }

    public function getById(int $id, int $patientId): array|false
    {
        return $this->db->fetchOne("SELECT * FROM wellness_goals WHERE id=? AND patient_id=?", [$id, $patientId]);
    }

    public function save(int $patientId, array $data, int $id = 0): void
    {
        if ($id) {
            $this->db->execute(
                "UPDATE wellness_goals SET title=?, description=?, category=?, target_date=?, status=?, updated_at=NOW()
                 WHERE id=? AND patient_id=?",
                [$data['title'], $data['description'] ?? null, $data['category'] ?? 'mental',
                 $data['target_date'] ?: null, $data['status'] ?? 'active', $id, $patientId]);
        } else {
            $this->db->insert(
                "INSERT INTO wellness_goals (patient_id, title, description, category, target_date, progress, status)
                 VALUES (?,?,?,?,?,0,'active')",
                [$patientId, $data['title'], $data['description'] ?? null,
                 $data['category'] ?? 'mental', $data['target_date'] ?: null]);
        }
    }

    public function updateProgress(int $id, int $patientId, int $progress): void
    {
        $status = $progress >= 100 ? 'completed' : 'active';
        $this->db->execute(
            "UPDATE wellness_goals SET progress=?, status=?, updated_at=NOW() WHERE id=? AND patient_id=?",
            [$progress, $status, $id, $patientId]);
    }

    public function delete(int $id, int $patientId): void
    {
        $this->db->execute("DELETE FROM wellness_goals WHERE id=? AND patient_id=?", [$id, $patientId]);
    }
}

class WellnessResource
{
    private Database $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function getAll(string $category = '', string $type = ''): array
    {
        $conditions = ["is_active=1"];
        $params = [];
        if ($category) { $conditions[] = "category=?"; $params[] = $category; }
        if ($type)     { $conditions[] = "type=?";     $params[] = $type; }
        $where = 'WHERE ' . implode(' AND ', $conditions);
        return $this->db->fetchAll(
            "SELECT * FROM wellness_resources $where ORDER BY is_featured DESC, view_count DESC", $params);
    }

    public function getById(int $id): array|false
    {
        $this->db->execute("UPDATE wellness_resources SET view_count=view_count+1 WHERE id=?", [$id]);
        return $this->db->fetchOne("SELECT * FROM wellness_resources WHERE id=?", [$id]);
    }

    public function getFeatured(int $limit = 3): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM wellness_resources WHERE is_featured=1 AND is_active=1 LIMIT ?", [$limit]);
    }

    public function save(array $data, int $id = 0): void
    {
        if ($id) {
            $this->db->execute(
                "UPDATE wellness_resources SET title=?, description=?, content=?, type=?, category=?, is_featured=?, is_active=?, updated_at=NOW() WHERE id=?",
                [$data['title'], $data['description'] ?? null, $data['content'] ?? null,
                 $data['type'], $data['category'],
                 isset($data['is_featured']) ? 1 : 0, isset($data['is_active']) ? 1 : 0, $id]);
        } else {
            $this->db->insert(
                "INSERT INTO wellness_resources (title, description, content, type, category, is_featured, is_active, created_by)
                 VALUES (?,?,?,?,?,?,?,?)",
                [$data['title'], $data['description'] ?? null, $data['content'] ?? null,
                 $data['type'], $data['category'], isset($data['is_featured']) ? 1 : 0, 1, Session::userId()]);
        }
    }

    public function delete(int $id): void
    {
        $this->db->execute("DELETE FROM wellness_resources WHERE id=?", [$id]);
    }
}
