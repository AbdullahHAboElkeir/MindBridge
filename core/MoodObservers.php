<?php

/**
 * Concrete Mood Observers
 * Handle specific reactions to mood changes
 */

/**
 * Therapist Alert Observer
 * Creates alerts for therapists when patients log concerning moods
 */
class TherapistAlertObserver implements MoodObserverInterface
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function update(array $moodData): void
    {
        $this->createTherapistAlert($moodData);
    }

    private function hasCrisisKeywords(array $moodData): bool
    {
        $text = strtolower(($moodData['notes'] ?? '') . ' ' . ($moodData['triggers'] ?? ''));
        foreach (CRISIS_KEYWORDS as $keyword) {
            if (str_contains($text, $keyword)) {
                return true;
            }
        }
        return false;
    }

    private function createTherapistAlert(array $moodData): void
    {
        // Get patient's assigned therapist
        $patient = $this->db->fetchOne(
            "SELECT assigned_therapist FROM patients WHERE id = ?",
            [$moodData['patient_id']]
        );

        if (!$patient || !$patient['assigned_therapist']) {
            return; // No therapist assigned
        }

        $moodLevel = $moodData['mood_level'];
        $isCritical = $moodLevel <= 3 || $this->hasCrisisKeywords($moodData);
        $message = $isCritical
            ? "Patient logged a concerning mood level {$moodLevel}. Please review their entry."
            : "Patient logged today's mood level {$moodLevel}.";

        $this->db->insert(
            "INSERT INTO mood_alerts (patient_id, therapist_id, mood_level, alert_type, message, status, created_at)
             VALUES (?, ?, ?, 'therapist_alert', ?, 'new', NOW())",
            [
                $moodData['patient_id'],
                $patient['assigned_therapist'],
                $moodLevel,
                $message
            ]
        );

        // Also create a notification so therapist sees the update in their inbox
        $this->db->insert(
            "INSERT INTO notifications (user_id, type, title, message, link, created_at)
             VALUES (?, 'mood_update', ?, ?, ?, NOW())",
            [
                $patient['assigned_therapist'],
                $isCritical ? 'Urgent Mood Update' : 'Mood Update',
                $message,
                '/therapist/patients'
            ]
        );
    }
}

/**
 * Crisis Alert Observer
 * Escalates to crisis management for severe cases
 */
class CrisisAlertObserver implements MoodObserverInterface
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function update(array $moodData): void
    {
        $moodLevel = $moodData['mood_level'];

        // Only for critical moods (1-2) or crisis keywords
        if ($moodLevel <= 2 || $this->hasCrisisKeywords($moodData)) {
            $this->createCrisisAlert($moodData);
        }
    }

    private function hasCrisisKeywords(array $moodData): bool
    {
        $text = strtolower(($moodData['notes'] ?? '') . ' ' . ($moodData['triggers'] ?? ''));
        foreach (CRISIS_KEYWORDS as $keyword) {
            if (str_contains($text, $keyword)) {
                return true;
            }
        }
        return false;
    }

    private function createCrisisAlert(array $moodData): void
    {
        $severity = $moodData['mood_level'] <= 1 ? 'critical' : 'high';

        $this->db->insert(
            "INSERT INTO crisis_alerts (patient_id, trigger_text, source, severity, status, created_at)
             VALUES (?, ?, 'mood', ?, 'new', NOW())",
            [
                $moodData['patient_id'],
                substr(($moodData['notes'] ?? '') . ' ' . ($moodData['triggers'] ?? ''), 0, 500),
                $severity
            ]
        );
    }
}

/**
 * Recommendation Observer
 * Generates personalized suggestions based on mood patterns
 */
class RecommendationObserver implements MoodObserverInterface
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function update(array $moodData): void
    {
        $recommendations = $this->generateRecommendations($moodData);

        foreach ($recommendations as $rec) {
            $this->saveRecommendation($moodData['patient_id'], $rec);
        }
    }

    private function generateRecommendations(array $moodData): array
    {
        $moodLevel = $moodData['mood_level'];
        $recommendations = [];

        if ($moodLevel <= 3) {
            $recommendations[] = [
                'type' => 'urgent_support',
                'title' => 'Immediate Support Recommended',
                'message' => 'Consider reaching out to your therapist or crisis hotline. You\'re not alone.',
                'priority' => 'high'
            ];
            $recommendations[] = [
                'type' => 'breathing_exercise',
                'title' => 'Try Deep Breathing',
                'message' => 'Practice the 4-7-8 breathing technique: inhale for 4 seconds, hold for 7, exhale for 8.',
                'priority' => 'medium'
            ];
        } elseif ($moodLevel <= 5) {
            $recommendations[] = [
                'type' => 'journaling',
                'title' => 'Journal Your Thoughts',
                'message' => 'Writing down your feelings can help process emotions and identify patterns.',
                'priority' => 'medium'
            ];
            $recommendations[] = [
                'type' => 'physical_activity',
                'title' => 'Light Physical Activity',
                'message' => 'A short walk or gentle stretching can help improve your mood naturally.',
                'priority' => 'low'
            ];
        } else {
            $recommendations[] = [
                'type' => 'maintenance',
                'title' => 'Keep Up the Good Work',
                'message' => 'Continue with activities that support your wellbeing. Consider sharing what\'s working well.',
                'priority' => 'low'
            ];
        }

        // Check for stress indicators in triggers
        $triggers = strtolower($moodData['triggers'] ?? '');
        if (str_contains($triggers, 'stress') || str_contains($triggers, 'work') || str_contains($triggers, 'anxiety')) {
            $recommendations[] = [
                'type' => 'stress_management',
                'title' => 'Stress Management Techniques',
                'message' => 'Try progressive muscle relaxation or mindfulness meditation to manage stress.',
                'priority' => 'medium'
            ];
        }

        return $recommendations;
    }

    private function saveRecommendation(int $patientId, array $rec): void
    {
        $this->db->insert(
            "INSERT INTO mood_recommendations (patient_id, recommendation_type, title, message, priority, status, created_at)
             VALUES (?, ?, ?, ?, ?, 'active', NOW())",
            [
                $patientId,
                $rec['type'],
                $rec['title'],
                $rec['message'],
                $rec['priority']
            ]
        );
    }
}