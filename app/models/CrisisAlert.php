<?php
class CrisisAlert extends Model
{
    private array $keywords = ['help', 'suicide', 'harm', 'crisis', 'panic', 'abuse'];

    public function detect(string $text): bool
    {
        $lower = strtolower($text);
        foreach ($this->keywords as $keyword) {
            if (strpos($lower, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }

    public function log(int $userId, string $message): int
    {
        $stmt = $this->db->prepare('INSERT INTO crisis_alerts (user_id, message, detected_at) VALUES (:user_id, :message, NOW())');
        $stmt->execute([':user_id' => $userId, ':message' => $message]);
        return (int)$this->db->lastInsertId();
    }
}
