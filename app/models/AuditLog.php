<?php
class AuditLog extends Model
{
    public static function record(int $userId, string $type, string $message): void
    {
        $instance = new self();
        $stmt = $instance->db->prepare('INSERT INTO audit_logs (user_id, event_type, message, created_at) VALUES (:user_id, :event_type, :message, NOW())');
        $stmt->execute([
            ':user_id' => $userId,
            ':event_type' => $type,
            ':message' => $message,
        ]);
    }

    public function recent(int $limit = 20): array
    {
        $stmt = $this->db->prepare('SELECT al.*, u.name AS user_name FROM audit_logs al LEFT JOIN users u ON u.id = al.user_id ORDER BY al.created_at DESC LIMIT :limit');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
