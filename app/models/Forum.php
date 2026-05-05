<?php

class ForumPost
{
    private Database $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function getAll(string $category = '', int $page = 1): array
    {
        $offset = ($page - 1) * ITEMS_PER_PAGE;
        $where  = $category ? "WHERE fp.status='published' AND fp.category=?" : "WHERE fp.status='published'";
        $params = $category ? [$category, ITEMS_PER_PAGE, $offset] : [ITEMS_PER_PAGE, $offset];
        return $this->db->fetchAll(
            "SELECT fp.*, u.first_name, u.last_name, u.role,
                    (SELECT COUNT(*) FROM forum_comments fc WHERE fc.post_id=fp.id AND fc.status='published') AS comment_count
             FROM forum_posts fp JOIN users u ON u.id=fp.user_id
             $where ORDER BY fp.is_pinned DESC, fp.created_at DESC LIMIT ? OFFSET ?", $params);
    }

    public function getById(int $id): array|false
    {
        $this->db->execute("UPDATE forum_posts SET view_count=view_count+1 WHERE id=?", [$id]);
        return $this->db->fetchOne(
            "SELECT fp.*, u.first_name, u.last_name, u.role
             FROM forum_posts fp JOIN users u ON u.id=fp.user_id WHERE fp.id=? AND fp.status='published'", [$id]);
    }

    public function create(int $userId, array $data): int
    {
        return $this->db->insert(
            "INSERT INTO forum_posts (user_id, title, content, category, is_anonymous, pseudonym, status, created_at)
             VALUES (?,?,?,?,?,?,'published',NOW())",
            [$userId, $data['title'], $data['content'], $data['category'] ?? 'general',
             isset($data['is_anonymous']) ? 1 : 0, $data['pseudonym'] ?? null]);
    }

    public function count(string $category = ''): int
    {
        $where = $category ? "WHERE status='published' AND category=?" : "WHERE status='published'";
        $params = $category ? [$category] : [];
        return (int)($this->db->fetchOne("SELECT COUNT(*) AS c FROM forum_posts $where", $params)['c'] ?? 0);
    }

    public function updateStatus(int $id, string $status): void
    {
        $this->db->execute("UPDATE forum_posts SET status=? WHERE id=?", [$status, $id]);
    }

    public function getComments(int $postId): array
    {
        return $this->db->fetchAll(
            "SELECT fc.*, u.first_name, u.last_name, u.role
             FROM forum_comments fc JOIN users u ON u.id=fc.user_id
             WHERE fc.post_id=? AND fc.status='published' ORDER BY fc.created_at ASC", [$postId]);
    }

    public function addComment(int $postId, int $userId, array $data): int
    {
        return $this->db->insert(
            "INSERT INTO forum_comments (post_id, user_id, content, is_anonymous, pseudonym, status, created_at)
             VALUES (?,?,?,?,?,'published',NOW())",
            [$postId, $userId, $data['content'], isset($data['is_anonymous']) ? 1 : 0, $data['pseudonym'] ?? null]);
    }

    public function getPending(): array
    {
        return $this->db->fetchAll(
            "SELECT fp.*, u.first_name, u.last_name FROM forum_posts fp JOIN users u ON u.id=fp.user_id
             WHERE fp.status='pending' ORDER BY fp.created_at DESC");
    }
}
