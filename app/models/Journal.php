<?php
// app/models/Journal.php

require_once __DIR__ . '/../../core/Model.php';

class Journal extends Model {
    protected $table = 'journals';

    public function getByPatient($patientId) {
        $stmt = $this->db->prepare("SELECT * FROM journals WHERE patient_id = ? ORDER BY created_at DESC");
        $stmt->execute([$patientId]);
        return $stmt->fetchAll();
    }
}
?>