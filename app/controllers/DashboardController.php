<?php

/**
 * Controller: Dashboard
 * Routes to role-specific dashboard views with stats.
 */
class DashboardController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(): void
    {
        Middleware::requireAuth();
        $role = Session::role();

        match ($role) {
            'patient'   => $this->patientDashboard(),
            'therapist' => $this->therapistDashboard(),
            'admin'     => $this->adminDashboard(),
            default     => $this->redirect('auth/logout'),
        };
    }

    private function patientDashboard(): void
    {
        $userId = Session::userId();
        $db = $this->db;

        // Get patient record
        $patient = $db->fetchOne(
            "SELECT p.*, u.first_name, u.last_name, u.email
             FROM patients p JOIN users u ON u.id = p.user_id
             WHERE p.user_id = ?", [$userId]
        );

        // Upcoming appointments
        $appointments = $db->fetchAll(
            "SELECT a.*, u.first_name AS t_first, u.last_name AS t_last
             FROM appointments a
             JOIN therapists t ON t.id = a.therapist_id
             JOIN users u ON u.id = t.user_id
             WHERE a.patient_id = ? AND a.status IN ('scheduled','confirmed')
               AND a.scheduled_at >= NOW()
             ORDER BY a.scheduled_at ASC LIMIT 3",
            [$patient['id'] ?? 0]
        );

        // Last 7 mood entries
        $moods = $db->fetchAll(
            "SELECT mood_level, entry_date FROM mood_entries
             WHERE patient_id = ?
             ORDER BY entry_date DESC LIMIT 7",
            [$patient['id'] ?? 0]
        );
        $moodLabels = array_reverse(array_column($moods, 'entry_date'));
        $moodValues = array_reverse(array_column($moods, 'mood_level'));

        // Today's mood
        $todayMood = $db->fetchOne(
            "SELECT * FROM mood_entries WHERE patient_id = ? AND entry_date = CURDATE()",
            [$patient['id'] ?? 0]
        );

        // Active goals count
        $goalCount = (int)($db->fetchOne(
            "SELECT COUNT(*) AS cnt FROM wellness_goals WHERE patient_id = ? AND status='active'",
            [$patient['id'] ?? 0]
        )['cnt'] ?? 0);

        // Total sessions
        $sessionCount = (int)($db->fetchOne(
            "SELECT COUNT(*) AS cnt FROM appointments a
             WHERE a.patient_id = ? AND a.status='completed'",
            [$patient['id'] ?? 0]
        )['cnt'] ?? 0);

        // Recent journal
        $recentJournal = $db->fetchOne(
            "SELECT * FROM journals WHERE patient_id = ? ORDER BY created_at DESC LIMIT 1",
            [$patient['id'] ?? 0]
        );

        // Unread messages
        $unreadMessages = (int)($db->fetchOne(
            "SELECT COUNT(*) AS cnt FROM messages WHERE receiver_id = ? AND is_read = 0",
            [$userId]
        )['cnt'] ?? 0);

        $pageTitle = 'My Dashboard';
        $this->view('dashboard.patient', compact(
            'pageTitle','patient','appointments','moods',
            'moodLabels','moodValues','todayMood',
            'goalCount','sessionCount','recentJournal','unreadMessages'
        ));
    }

    private function therapistDashboard(): void
    {
        $userId = Session::userId();
        $db = $this->db;

        $therapist = $db->fetchOne(
            "SELECT t.*, u.first_name, u.last_name, u.email
             FROM therapists t JOIN users u ON u.id = t.user_id
             WHERE t.user_id = ?", [$userId]
        );

        // Today's appointments
        $todayAppointments = $db->fetchAll(
            "SELECT a.*, u.first_name AS p_first, u.last_name AS p_last
             FROM appointments a
             JOIN patients p ON p.id = a.patient_id
             JOIN users u ON u.id = p.user_id
             WHERE a.therapist_id = ? AND DATE(a.scheduled_at) = CURDATE()
             ORDER BY a.scheduled_at ASC",
            [$therapist['id'] ?? 0]
        );

        // Upcoming (next 7 days)
        $upcomingAppointments = $db->fetchAll(
            "SELECT a.*, u.first_name AS p_first, u.last_name AS p_last
             FROM appointments a
             JOIN patients p ON p.id = a.patient_id
             JOIN users u ON u.id = p.user_id
             WHERE a.therapist_id = ? AND a.scheduled_at BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)
               AND a.status IN ('scheduled','confirmed')
             ORDER BY a.scheduled_at ASC LIMIT 5",
            [$therapist['id'] ?? 0]
        );

        // Session counts this month
        $monthSessions = (int)($db->fetchOne(
            "SELECT COUNT(*) AS cnt FROM appointments
             WHERE therapist_id = ? AND status='completed'
               AND MONTH(scheduled_at)=MONTH(NOW()) AND YEAR(scheduled_at)=YEAR(NOW())",
            [$therapist['id'] ?? 0]
        )['cnt'] ?? 0);

        $totalPatients = (int)($therapist['current_patients'] ?? 0);
        $rating = number_format((float)($therapist['rating'] ?? 0), 1);

        // Unread messages
        $unreadMessages = (int)($db->fetchOne(
            "SELECT COUNT(*) AS cnt FROM messages WHERE receiver_id = ? AND is_read = 0", [$userId]
        )['cnt'] ?? 0);

        // Monthly sessions for chart (last 6 months)
        $sessChart = $db->fetchAll(
            "SELECT DATE_FORMAT(scheduled_at, '%b') AS month, COUNT(*) AS cnt
             FROM appointments WHERE therapist_id = ? AND status='completed'
               AND scheduled_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
             GROUP BY MONTH(scheduled_at), month ORDER BY scheduled_at ASC",
            [$therapist['id'] ?? 0]
        );
        $sessLabels = array_column($sessChart, 'month');
        $sessValues = array_column($sessChart, 'cnt');

        $pageTitle = 'Therapist Dashboard';
        $this->view('dashboard.therapist', compact(
            'pageTitle','therapist','todayAppointments','upcomingAppointments',
            'monthSessions','totalPatients','rating','unreadMessages',
            'sessLabels','sessValues'
        ));
    }

    private function adminDashboard(): void
    {
        $db = $this->db;

        // Safe defaults
        $totalUsers = $totalPatients = $totalTherapists = $totalSessions = 0;
        $pendingReports = $crisisNew = 0;
        $monthRevenue = 0.0;
        $recentUsers = $recentCrisis = [];
        $sessLabels = $sessValues = [];

        try { $totalUsers = (int)($db->fetchOne("SELECT COUNT(*) AS c FROM users")['c'] ?? 0); } catch (Exception $e) {}
        try { $totalPatients = (int)($db->fetchOne("SELECT COUNT(*) AS c FROM patients")['c'] ?? 0); } catch (Exception $e) {}
        try { $totalTherapists = (int)($db->fetchOne("SELECT COUNT(*) AS c FROM therapists")['c'] ?? 0); } catch (Exception $e) {}
        try { $totalSessions = (int)($db->fetchOne("SELECT COUNT(*) AS c FROM appointments WHERE status='completed'")['c'] ?? 0); } catch (Exception $e) {}
        try { $pendingReports = (int)($db->fetchOne("SELECT COUNT(*) AS c FROM reports WHERE status='pending'")['c'] ?? 0); } catch (Exception $e) {}
        try { $crisisNew = (int)($db->fetchOne("SELECT COUNT(*) AS c FROM crisis_alerts WHERE status='new'")['c'] ?? 0); } catch (Exception $e) {}
        try {
            $monthRevenue = (float)($db->fetchOne(
                "SELECT COALESCE(SUM(amount),0) AS s FROM payments WHERE status='paid'
                 AND MONTH(paid_at)=MONTH(NOW()) AND YEAR(paid_at)=YEAR(NOW())"
            )['s'] ?? 0);
        } catch (Exception $e) {}

        try {
            $recentUsers = $db->fetchAll(
                "SELECT id, first_name, last_name, role, status, created_at FROM users
                 ORDER BY created_at DESC LIMIT 8"
            );
        } catch (Exception $e) {}

        try {
            $recentCrisis = $db->fetchAll(
                "SELECT ca.*, u.first_name, u.last_name FROM crisis_alerts ca
                 JOIN patients p ON p.id = ca.patient_id
                 JOIN users u ON u.id = p.user_id
                 WHERE ca.status = 'new'
                 ORDER BY ca.created_at DESC LIMIT 5"
            );
        } catch (Exception $e) {}

        try {
            $sessChart  = $db->fetchAll(
                "SELECT DATE_FORMAT(scheduled_at,'%b') AS month, COUNT(*) AS cnt
                 FROM appointments WHERE scheduled_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                 GROUP BY MONTH(scheduled_at), month ORDER BY scheduled_at ASC"
            );
            $sessLabels = array_column($sessChart, 'month');
            $sessValues = array_column($sessChart, 'cnt');
        } catch (Exception $e) {}

        $pageTitle = 'Admin Dashboard';
        $this->view('dashboard.admin', compact(
            'pageTitle','totalUsers','totalPatients','totalTherapists',
            'totalSessions','pendingReports','crisisNew','monthRevenue',
            'recentUsers','recentCrisis','sessLabels','sessValues'
        ));
    }
}

