<?php

class Appointment
{
    private Database $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function getById(int $id): array|false
    {
        return $this->db->fetchOne(
            "SELECT a.*,
                    pu.first_name AS p_first, pu.last_name AS p_last, pu.email AS p_email,
                    tu.first_name AS t_first, tu.last_name AS t_last, tu.email AS t_email
             FROM appointments a
             JOIN patients p ON p.id = a.patient_id
             JOIN users pu ON pu.id = p.user_id
             JOIN therapists t ON t.id = a.therapist_id
             JOIN users tu ON tu.id = t.user_id
             WHERE a.id = ?", [$id]);
    }

    public function getForPatient(int $patientId, string $status = ''): array
    {
        $where = $status ? "AND a.status = '$status'" : "";
        return $this->db->fetchAll(
            "SELECT a.*, tu.first_name AS t_first, tu.last_name AS t_last
             FROM appointments a
             JOIN therapists t ON t.id = a.therapist_id
             JOIN users tu ON tu.id = t.user_id
             WHERE a.patient_id = ? $where
             ORDER BY a.scheduled_at DESC", [$patientId]);
    }

    public function getForTherapist(int $therapistId, string $status = ''): array
    {
        $where = $status ? "AND a.status = '$status'" : "";
        return $this->db->fetchAll(
            "SELECT a.*, pu.first_name AS p_first, pu.last_name AS p_last
             FROM appointments a
             JOIN patients p ON p.id = a.patient_id
             JOIN users pu ON pu.id = p.user_id
             WHERE a.therapist_id = ? $where
             ORDER BY a.scheduled_at DESC", [$therapistId]);
    }

    /** Check for double-booking conflict */
    public function hasConflict(int $therapistId, string $datetime, int $duration, int $excludeId = 0): bool
    {
        $end = date('Y-m-d H:i:s', strtotime($datetime) + $duration * 60);
        $row = $this->db->fetchOne(
            "SELECT id FROM appointments
             WHERE therapist_id = ? AND id != ? AND status NOT IN ('cancelled','no_show')
               AND scheduled_at < ? AND DATE_ADD(scheduled_at, INTERVAL duration_minutes MINUTE) > ?",
            [$therapistId, $excludeId, $end, $datetime]);
        return !empty($row);
    }

    public function book(int $patientId, int $therapistId, string $datetime, int $duration, string $type, string $notes = ''): int
    {
        $id = $this->db->insert(
            "INSERT INTO appointments (patient_id, therapist_id, scheduled_at, duration_minutes, type, status, patient_notes, created_at)
             VALUES (?,?,?,?,?,'scheduled',?,NOW())",
            [$patientId, $therapistId, $datetime, $duration, $type, $notes]);

        // Create pending payment
        $therapist = $this->db->fetchOne("SELECT session_rate FROM therapists WHERE id=?", [$therapistId]);
        if ($therapist) {
            $this->db->insert(
                "INSERT INTO payments (appointment_id, patient_id, amount, status, created_at)
                 VALUES (?,?,?,'pending',NOW())",
                [$id, $patientId, $therapist['session_rate']]);
        }
        return $id;
    }

    public function cancel(int $id, int $cancelledBy, string $reason = ''): bool
    {
        return $this->db->execute(
            "UPDATE appointments SET status='cancelled', cancel_reason=?, cancelled_by=?, updated_at=NOW()
             WHERE id=?",
            [$reason, $cancelledBy, $id]) > 0;
    }

    public function reschedule(int $id, string $newDatetime): bool
    {
        return $this->db->execute(
            "UPDATE appointments SET scheduled_at=?, status='scheduled', updated_at=NOW() WHERE id=?",
            [$newDatetime, $id]) > 0;
    }

    public function updateStatus(int $id, string $status): void
    {
        $this->db->execute("UPDATE appointments SET status=?, updated_at=NOW() WHERE id=?", [$status, $id]);
    }

    public function getAvailableSlots(int $therapistId, string $date): array
    {
        $dow = (int)date('w', strtotime($date));
        $availability = $this->db->fetchAll(
            "SELECT start_time, end_time FROM therapist_availability
             WHERE therapist_id=? AND day_of_week=? AND is_active=1",
            [$therapistId, $dow]);

        $booked = $this->db->fetchAll(
            "SELECT scheduled_at, duration_minutes FROM appointments
             WHERE therapist_id=? AND DATE(scheduled_at)=? AND status NOT IN ('cancelled','no_show')",
            [$therapistId, $date]);

        $slots = [];
        foreach ($availability as $avail) {
            $start = strtotime("$date {$avail['start_time']}");
            $end   = strtotime("$date {$avail['end_time']}");

            while ($start + 50*60 <= $end) {
                $slotEnd = $start + 50*60;
                $slotStr = date('H:i', $start);
                $conflict = false;

                foreach ($booked as $b) {
                    $bStart = strtotime($b['scheduled_at']);
                    $bEnd   = $bStart + ($b['duration_minutes'] * 60);
                    if ($start < $bEnd && $slotEnd > $bStart) {
                        $conflict = true; break;
                    }
                }

                if (!$conflict && $start > time()) {
                    $slots[] = $slotStr;
                }
                $start += 60*60; // 1-hour blocks
            }
        }
        return $slots;
    }
}

class SessionRecord
{
    private Database $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function getByAppointmentId(int $appointmentId): array|false
    {
        return $this->db->fetchOne("SELECT * FROM sessions WHERE appointment_id=?", [$appointmentId]);
    }

    public function getForPatient(int $patientId): array
    {
        return $this->db->fetchAll(
            "SELECT s.*, a.scheduled_at, a.type, a.status AS appt_status,
                    tu.first_name AS t_first, tu.last_name AS t_last
             FROM sessions s
             JOIN appointments a ON a.id = s.appointment_id
             JOIN therapists t ON t.id = a.therapist_id
             JOIN users tu ON tu.id = t.user_id
             WHERE a.patient_id=?
             ORDER BY a.scheduled_at DESC", [$patientId]);
    }

    public function getForTherapist(int $therapistId): array
    {
        return $this->db->fetchAll(
            "SELECT s.*, a.scheduled_at, a.type,
                    pu.first_name AS p_first, pu.last_name AS p_last
             FROM sessions s
             JOIN appointments a ON a.id = s.appointment_id
             JOIN patients p ON p.id = a.patient_id
             JOIN users pu ON pu.id = p.user_id
             WHERE a.therapist_id=?
             ORDER BY a.scheduled_at DESC", [$therapistId]);
    }

    public function saveNotes(int $appointmentId, array $data): void
    {
        $existing = $this->getByAppointmentId($appointmentId);
        if ($existing) {
            $this->db->execute(
                "UPDATE sessions SET therapist_notes=?, techniques_used=?, homework=?,
                 outcome=?, follow_up_date=?, updated_at=NOW()
                 WHERE appointment_id=?",
                [$data['therapist_notes'] ?? null, $data['techniques_used'] ?? null,
                 $data['homework'] ?? null, $data['outcome'] ?? null,
                 $data['follow_up_date'] ?: null, $appointmentId]);
        } else {
            $this->db->insert(
                "INSERT INTO sessions (appointment_id, therapist_notes, techniques_used,
                 homework, outcome, follow_up_date, started_at, ended_at)
                 VALUES (?,?,?,?,?,?,?,?)",
                [$appointmentId, $data['therapist_notes'] ?? null, $data['techniques_used'] ?? null,
                 $data['homework'] ?? null, $data['outcome'] ?? null,
                 $data['follow_up_date'] ?: null,
                 $data['started_at'] ?? null, $data['ended_at'] ?? null]);
        }
    }
}
