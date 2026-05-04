<?php
class Admin extends Model
{
    public function dashboardMetrics(): array
    {
        $metrics = [];
        $metrics['active_users'] = (int)$this->db->query('SELECT COUNT(*) FROM users WHERE status = "active"')->fetchColumn();
        $metrics['appointments'] = (int)$this->db->query('SELECT COUNT(*) FROM appointments')->fetchColumn();
        $metrics['resources'] = (int)$this->db->query('SELECT COUNT(*) FROM resources')->fetchColumn();
        return $metrics;
    }
}
