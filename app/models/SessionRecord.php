<?php

class SessionRecord
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all session records for a therapist
     */
    public function getForTherapist(int $therapistId): array
    {
        return $this->db->fetchAll(
            "SELECT s.*, a.scheduled_at, a.type, a.patient_id, a.status,
                    pu.first_name AS p_first, pu.last_name AS p_last
             FROM sessions s
             JOIN appointments a ON a.id = s.appointment_id
             JOIN patients p ON p.id = a.patient_id
             JOIN users pu ON pu.id = p.user_id
             WHERE a.therapist_id = ?
             ORDER BY a.scheduled_at DESC",
            [$therapistId]
        );
    }

    /**
     * Get session record by appointment ID
     */
    public function getByAppointmentId(int $appointmentId): array|false
    {
        return $this->db->fetchOne(
            "SELECT s.*, a.scheduled_at, a.type, a.patient_id, a.status,
                    pu.first_name AS p_first, pu.last_name AS p_last,
                    tu.first_name AS t_first, tu.last_name AS t_last
             FROM sessions s
             JOIN appointments a ON a.id = s.appointment_id
             JOIN patients p ON p.id = a.patient_id
             JOIN users pu ON pu.id = p.user_id
             JOIN therapists t ON t.id = a.therapist_id
             JOIN users tu ON tu.id = t.user_id
             WHERE s.appointment_id = ?",
            [$appointmentId]
        );
    }

    /**
     * Save or update session notes
     */
    public function saveNotes(int $appointmentId, array $data): void
    {
        $existing = $this->db->fetchOne("SELECT id FROM sessions WHERE appointment_id = ?", [$appointmentId]);

        if ($existing) {
            $this->db->execute(
                "UPDATE sessions SET therapist_notes = ?, techniques_used = ?, homework = ?,
                 outcome = ?, follow_up_date = ?, updated_at = NOW()
                 WHERE appointment_id = ?",
                [
                    $data['therapist_notes'] ?? null,
                    $data['techniques_used'] ?? null,
                    $data['homework'] ?? null,
                    $data['outcome'] ?? null,
                    $data['follow_up_date'] ?? null,
                    $appointmentId
                ]
            );
        } else {
            $this->db->insert(
                "INSERT INTO sessions (appointment_id, therapist_notes, techniques_used, homework, outcome, follow_up_date)
                 VALUES (?, ?, ?, ?, ?, ?)",
                [
                    $appointmentId,
                    $data['therapist_notes'] ?? null,
                    $data['techniques_used'] ?? null,
                    $data['homework'] ?? null,
                    $data['outcome'] ?? null,
                    $data['follow_up_date'] ?? null
                ]
            );
        }
    }

    /**
     * Get session statistics for a therapist
     */
    public function getStats(int $therapistId, int $days = 30): array
    {
        $stats = $this->db->fetchOne(
            "SELECT
                COUNT(*) as total_sessions,
                AVG(duration_actual) as avg_duration,
                SUM(CASE WHEN outcome = 'good' THEN 1 ELSE 0 END) as good_outcomes,
                SUM(CASE WHEN outcome = 'poor' THEN 1 ELSE 0 END) as poor_outcomes
             FROM sessions s
             JOIN appointments a ON a.id = s.appointment_id
             WHERE a.therapist_id = ? AND a.scheduled_at >= DATE_SUB(NOW(), INTERVAL ? DAY)",
            [$therapistId, $days]
        );

        return [
            'total_sessions' => (int)($stats['total_sessions'] ?? 0),
            'avg_duration' => round((float)($stats['avg_duration'] ?? 0), 1),
            'good_outcomes' => (int)($stats['good_outcomes'] ?? 0),
            'poor_outcomes' => (int)($stats['poor_outcomes'] ?? 0),
            'success_rate' => $stats['total_sessions'] > 0 ?
                round(($stats['good_outcomes'] / $stats['total_sessions']) * 100, 1) : 0
        ];
    }
}