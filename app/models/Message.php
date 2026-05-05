<?php

class Message
{
    private Database $db;
    public function __construct() { $this->db = Database::getInstance(); }

    /**
     * Get latest message per conversation thread.
     * Fixed: reference derived-table alias as t.other_id to avoid ambiguity.
     */
    public function getConversations(int $userId): array
    {
        return $this->db->fetchAll(
            "SELECT m.*,
                    t.other_user_id,
                    u.first_name, u.last_name, u.role, u.avatar,
                    (SELECT COUNT(*) FROM messages m2
                     WHERE m2.sender_id   = t.other_user_id
                       AND m2.receiver_id = ?
                       AND m2.is_read     = 0) AS unread_count
             FROM messages m
             JOIN (
               SELECT IF(sender_id=?, receiver_id, sender_id) AS other_user_id,
                      MAX(id) AS max_id
               FROM messages
               WHERE sender_id = ? OR receiver_id = ?
               GROUP BY IF(sender_id=?, receiver_id, sender_id)
             ) t ON m.id = t.max_id
             JOIN users u ON u.id = t.other_user_id
             ORDER BY m.created_at DESC",
            [$userId, $userId, $userId, $userId, $userId]);
    }

    public function getThread(int $userId, int $otherId): array
    {
        // Mark incoming messages as read
        $this->db->execute(
            "UPDATE messages SET is_read = 1, read_at = NOW()
             WHERE sender_id = ? AND receiver_id = ? AND is_read = 0",
            [$otherId, $userId]);

        return $this->db->fetchAll(
            "SELECT m.*, u.first_name, u.last_name, u.avatar
             FROM messages m JOIN users u ON u.id = m.sender_id
             WHERE (m.sender_id = ? AND m.receiver_id = ?)
                OR (m.sender_id = ? AND m.receiver_id = ?)
             ORDER BY m.created_at ASC",
            [$userId, $otherId, $otherId, $userId]);
    }

    public function send(int $senderId, int $receiverId, string $content, string $subject = ''): int
    {
        return $this->db->insert(
            "INSERT INTO messages (sender_id, receiver_id, subject, content, is_read, created_at)
             VALUES (?, ?, ?, ?, 0, NOW())",
            [$senderId, $receiverId, $subject ?: null, $content]);
    }

    public function getUnreadCount(int $userId): int
    {
        return (int)($this->db->fetchOne(
            "SELECT COUNT(*) AS c FROM messages WHERE receiver_id = ? AND is_read = 0",
            [$userId])['c'] ?? 0);
    }

    /**
     * Return valid messaging contacts for the current user.
     * Patients → their assigned therapist.
     * Therapists → their assigned patients.
     * Admins → all active users.
     */
    public function getContacts(int $userId): array
    {
        $role = Session::role();

        if ($role === 'patient') {
            // The patient's assigned therapist (stored as therapist user_id in patients.assigned_therapist)
            return $this->db->fetchAll(
                "SELECT u.id, u.first_name, u.last_name, u.role, u.avatar
                 FROM users u
                 JOIN patients p ON p.assigned_therapist = u.id
                 WHERE p.user_id = ?",
                [$userId]);
        }

        if ($role === 'therapist') {
            // All patients whose assigned_therapist = this therapist's user_id
            return $this->db->fetchAll(
                "SELECT u.id, u.first_name, u.last_name, u.role, u.avatar
                 FROM users u
                 JOIN patients p ON p.user_id = u.id
                 WHERE p.assigned_therapist = ?",
                [$userId]);
        }

        // Admin: all active non-admin users
        return $this->db->fetchAll(
            "SELECT id, first_name, last_name, role, avatar
             FROM users
             WHERE id != ? AND status = 'active'
             ORDER BY role, first_name",
            [$userId]);
    }
}
