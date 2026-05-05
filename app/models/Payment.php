<?php

class Payment
{
    private Database $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function getForPatient(int $patientId): array
    {
        return $this->db->fetchAll(
            "SELECT py.*, a.scheduled_at, a.type, tu.first_name AS t_first, tu.last_name AS t_last
             FROM payments py
             JOIN appointments a ON a.id=py.appointment_id
             JOIN therapists t ON t.id=a.therapist_id
             JOIN users tu ON tu.id=t.user_id
             WHERE py.patient_id=?
             ORDER BY py.created_at DESC", [$patientId]);
    }

    public function process(int $paymentId, string $method): bool
    {
        $ref = strtoupper('TXN-'.date('Y-m-d').'-'.rand(1000,9999));
        return $this->db->execute(
            "UPDATE payments SET status='paid', method=?, transaction_ref=?, paid_at=NOW() WHERE id=? AND status='pending'",
            [$method, $ref, $paymentId]) > 0;
    }

    public function getSummary(int $patientId): array
    {
        $total = (float)($this->db->fetchOne(
            "SELECT COALESCE(SUM(amount),0) AS s FROM payments WHERE patient_id=? AND status='paid'",
            [$patientId])['s'] ?? 0);
        $pending = (float)($this->db->fetchOne(
            "SELECT COALESCE(SUM(amount),0) AS s FROM payments WHERE patient_id=? AND status='pending'",
            [$patientId])['s'] ?? 0);
        return compact('total','pending');
    }
}
