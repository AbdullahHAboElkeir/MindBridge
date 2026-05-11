<?php

/**
 * Patient Report PDF Generator
 * Generates comprehensive patient progress reports for therapists
 */
class PatientReportPDF
{
    private TCPDF $pdf;
    private Database $db;

    public function __construct()
    {
        require_once BASE_PATH . '/vendor/tcpdf/tcpdf.php';
        $this->pdf = new TCPDF('P', 'mm', 'A4');
        $this->db = Database::getInstance();

        // Set document properties
        $this->pdf->SetMargins(20, 25, 20);
        $this->pdf->SetFont('helvetica', '', 11);
    }

    /**
     * Generate patient report PDF
     */
    public function generateReport(int $patientId, int $therapistId, array $options = []): void
    {
        // Load required models
        require_once BASE_PATH . '/app/models/Wellness.php';
        require_once BASE_PATH . '/app/models/SessionRecord.php';
        
        // Verify therapist has access to this patient
        $accessCheck = $this->db->fetchOne(
            "SELECT p.id FROM patients p
             JOIN therapists t ON t.user_id = p.assigned_therapist
             WHERE p.id = ? AND t.id = ?",
            [$patientId, $therapistId]
        );

        if (!$accessCheck) {
            throw new Exception('Access denied: Therapist does not have permission to view this patient.');
        }

        // Get patient data
        $patient = $this->getPatientData($patientId);
        $sessions = $this->getSessionHistory($patientId, $therapistId);
        $moodData = $this->getMoodData($patientId);
        $goals = $this->getGoalsData($patientId);
        $intake = $this->getIntakeData($patientId);

        // Generate PDF content
        $this->addHeader($patient, $options);
        $this->addPatientInfo($patient);
        $this->addIntakeAssessment($intake);
        $this->addSessionHistory($sessions);
        $this->addMoodTracking($moodData);
        $this->addGoalsProgress($goals);
        $this->addTherapistSummary($options);

        // Output PDF
        $filename = "Patient_Report_{$patient['first_name']}_{$patient['last_name']}_" . date('Y-m-d') . ".pdf";
        $this->pdf->Output($filename, 'D');
    }

    private function addHeader(array $patient, array $options): void
    {
        $this->pdf->AddPage();

        // Title
        $this->pdf->SetFont('helvetica', 'B', 16);
        $this->pdf->Cell(0, 10, 'MindBridge Patient Progress Report', 0, 1, 'C');
        $this->pdf->Ln(5);

        // Patient name and report date
        $this->pdf->SetFont('helvetica', 'B', 14);
        $this->pdf->Cell(0, 8, $patient['first_name'] . ' ' . $patient['last_name'], 0, 1, 'C');
        $this->pdf->SetFont('helvetica', '', 11);
        $this->pdf->Cell(0, 6, 'Report Generated: ' . date('F j, Y'), 0, 1, 'C');
        $this->pdf->Ln(10);

        // Report period
        if (isset($options['start_date']) && isset($options['end_date'])) {
            $this->pdf->Cell(0, 6, 'Period: ' . date('M j, Y', strtotime($options['start_date'])) . ' - ' . date('M j, Y', strtotime($options['end_date'])), 0, 1, 'C');
        }
        $this->pdf->Ln(5);
    }

    private function addPatientInfo(array $patient): void
    {
        $this->pdf->SetFont('helvetica', 'B', 12);
        $this->pdf->Cell(0, 8, 'Patient Information', 0, 1);
        $this->pdf->SetFont('helvetica', '', 11);

        $this->pdf->Cell(40, 6, 'Name:', 0, 0);
        $this->pdf->Cell(0, 6, $patient['first_name'] . ' ' . $patient['last_name'], 0, 1);

        $this->pdf->Cell(40, 6, 'Email:', 0, 0);
        $this->pdf->Cell(0, 6, $patient['email'], 0, 1);

        if ($patient['phone']) {
            $this->pdf->Cell(40, 6, 'Phone:', 0, 0);
            $this->pdf->Cell(0, 6, $patient['phone'], 0, 1);
        }

        if ($patient['date_of_birth']) {
            $age = date_diff(date_create($patient['date_of_birth']), date_create('today'))->y;
            $this->pdf->Cell(40, 6, 'Age:', 0, 0);
            $this->pdf->Cell(0, 6, $age . ' years old', 0, 1);
        }

        $this->pdf->Cell(40, 6, 'Preferred Language:', 0, 0);
        $this->pdf->Cell(0, 6, $patient['preferred_language'], 0, 1);

        $this->pdf->Ln(5);
    }

    private function addIntakeAssessment(array $intake): void
    {
        if (empty($intake)) return;

        $this->pdf->SetFont('helvetica', 'B', 12);
        $this->pdf->Cell(0, 8, 'Initial Assessment', 0, 1);
        $this->pdf->SetFont('helvetica', '', 11);

        if ($intake['primary_concerns']) {
            $this->pdf->Cell(40, 6, 'Primary Concerns:', 0, 1);
            $this->pdf->MultiCell(0, 6, $intake['primary_concerns']);
            $this->pdf->Ln(3);
        }

        if ($intake['goals']) {
            $this->pdf->Cell(40, 6, 'Treatment Goals:', 0, 1);
            $this->pdf->MultiCell(0, 6, $intake['goals']);
            $this->pdf->Ln(3);
        }

        if ($intake['previous_therapy'] == 1) {
            $this->pdf->Cell(0, 6, 'Previous Therapy Experience: Yes', 0, 1);
        } else {
            $this->pdf->Cell(0, 6, 'Previous Therapy Experience: No', 0, 1);
        }

        $this->pdf->Ln(5);
    }

    private function addSessionHistory(array $sessions): void
    {
        $this->pdf->SetFont('helvetica', 'B', 12);
        $this->pdf->Cell(0, 8, 'Session History', 0, 1);
        $this->pdf->SetFont('helvetica', '', 11);

        if (empty($sessions)) {
            $this->pdf->Cell(0, 6, 'No completed sessions found.', 0, 1);
            $this->pdf->Ln(5);
            return;
        }

        foreach ($sessions as $session) {
            $this->pdf->SetFont('helvetica', 'B', 11);
            $this->pdf->Cell(0, 7, date('M j, Y', strtotime($session['scheduled_at'])) . ' - ' . ucfirst($session['type']) . ' Session', 0, 1);
            $this->pdf->SetFont('helvetica', '', 11);

            if ($session['therapist_notes']) {
                $this->pdf->Cell(30, 6, 'Notes:', 0, 1);
                $this->pdf->MultiCell(0, 6, $session['therapist_notes']);
                $this->pdf->Ln(2);
            }

            if ($session['outcome']) {
                $this->pdf->Cell(30, 6, 'Outcome:', 0, 0);
                $this->pdf->Cell(0, 6, ucfirst($session['outcome']), 0, 1);
            }

            if ($session['techniques_used']) {
                $this->pdf->Cell(30, 6, 'Techniques:', 0, 1);
                $this->pdf->MultiCell(0, 6, $session['techniques_used']);
            }

            if ($session['homework']) {
                $this->pdf->Cell(30, 6, 'Homework:', 0, 1);
                $this->pdf->MultiCell(0, 6, $session['homework']);
            }

            $this->pdf->Ln(5);
        }
    }

    private function addMoodTracking(array $moodData): void
    {
        $this->pdf->SetFont('helvetica', 'B', 12);
        $this->pdf->Cell(0, 8, 'Mood Tracking Summary', 0, 1);
        $this->pdf->SetFont('helvetica', '', 11);

        if (empty($moodData)) {
            $this->pdf->Cell(0, 6, 'No mood tracking data available.', 0, 1);
            $this->pdf->Ln(5);
            return;
        }

        // Calculate averages
        $totalEntries = count($moodData);
        $avgMood = array_sum(array_column($moodData, 'mood_level')) / $totalEntries;

        $this->pdf->Cell(50, 6, 'Total Entries:', 0, 0);
        $this->pdf->Cell(0, 6, $totalEntries, 0, 1);

        $this->pdf->Cell(50, 6, 'Average Mood (1-10):', 0, 0);
        $this->pdf->Cell(0, 6, number_format($avgMood, 1), 0, 1);

        // Recent entries (last 10)
        $recent = array_slice($moodData, -10);
        if (!empty($recent)) {
            $this->pdf->Ln(3);
            $this->pdf->Cell(0, 6, 'Recent Mood Entries:', 0, 1);
            foreach ($recent as $entry) {
                $this->pdf->Cell(30, 6, date('M j', strtotime($entry['entry_date'])), 0, 0);
                $this->pdf->Cell(20, 6, 'Level: ' . $entry['mood_level'], 0, 0);
                if ($entry['notes']) {
                    $this->pdf->Cell(0, 6, substr($entry['notes'], 0, 50) . (strlen($entry['notes']) > 50 ? '...' : ''), 0, 1);
                } else {
                    $this->pdf->Ln(6);
                }
            }
        }

        $this->pdf->Ln(5);
    }

    private function addGoalsProgress(array $goals): void
    {
        $this->pdf->SetFont('helvetica', 'B', 12);
        $this->pdf->Cell(0, 8, 'Goals & Progress', 0, 1);
        $this->pdf->SetFont('helvetica', '', 11);

        if (empty($goals)) {
            $this->pdf->Cell(0, 6, 'No wellness goals set.', 0, 1);
            $this->pdf->Ln(5);
            return;
        }

        foreach ($goals as $goal) {
            $this->pdf->SetFont('helvetica', 'B', 11);
            $this->pdf->Cell(0, 7, $goal['title'], 0, 1);
            $this->pdf->SetFont('helvetica', '', 11);

            if ($goal['description']) {
                $this->pdf->MultiCell(0, 6, $goal['description']);
            }

            $this->pdf->Cell(40, 6, 'Status:', 0, 0);
            $this->pdf->Cell(0, 6, ucfirst($goal['status']), 0, 1);

            $this->pdf->Cell(40, 6, 'Progress:', 0, 0);
            $this->pdf->Cell(0, 6, $goal['progress'] . '%', 0, 1);

            if ($goal['target_date']) {
                $this->pdf->Cell(40, 6, 'Target Date:', 0, 0);
                $this->pdf->Cell(0, 6, date('M j, Y', strtotime($goal['target_date'])), 0, 1);
            }

            $this->pdf->Ln(3);
        }

        $this->pdf->Ln(5);
    }

    private function addTherapistSummary(array $options): void
    {
        $this->pdf->SetFont('helvetica', 'B', 12);
        $this->pdf->Cell(0, 8, 'Therapist Summary', 0, 1);
        $this->pdf->SetFont('helvetica', '', 11);

        if (isset($options['summary']) && !empty($options['summary'])) {
            $this->pdf->MultiCell(0, 6, $options['summary']);
        } else {
            $this->pdf->Cell(0, 6, 'No summary provided.', 0, 1);
        }

        $this->pdf->Ln(10);

        // Footer
        $this->pdf->SetFont('helvetica', 'I', 9);
        $this->pdf->Cell(0, 5, 'This report was generated by MindBridge on ' . date('F j, Y \a\t g:i A'), 0, 1, 'C');
        $this->pdf->Cell(0, 5, 'Confidential patient information - For therapeutic use only', 0, 1, 'C');
    }

    private function getPatientData(int $patientId): array
    {
        return $this->db->fetchOne(
            "SELECT p.*, u.first_name, u.last_name, u.email, u.phone, u.date_of_birth, u.gender
             FROM patients p JOIN users u ON u.id = p.user_id WHERE p.id = ?",
            [$patientId]
        );
    }

    private function getSessionHistory(int $patientId, int $therapistId): array
    {
        return $this->db->fetchAll(
            "SELECT s.*, a.scheduled_at, a.type, a.status
             FROM sessions s
             JOIN appointments a ON a.id = s.appointment_id
             WHERE a.patient_id = ? AND a.therapist_id = ? AND a.status = 'completed'
             ORDER BY a.scheduled_at DESC",
            [$patientId, $therapistId]
        );
    }

    private function getMoodData(int $patientId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM mood_entries WHERE patient_id = ? ORDER BY entry_date DESC LIMIT 30",
            [$patientId]
        );
    }

    private function getGoalsData(int $patientId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM wellness_goals WHERE patient_id = ? ORDER BY created_at DESC",
            [$patientId]
        );
    }

    private function getIntakeData(int $patientId): array
    {
        return $this->db->fetchOne(
            "SELECT * FROM intake_forms WHERE patient_id = ? AND status = 'submitted'",
            [$patientId]
        ) ?: [];
    }
}
?>