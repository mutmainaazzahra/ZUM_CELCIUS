<?php
class Notification
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($userId, $message, $status = 'sent')
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO notifications (user_id, message, status, is_read) VALUES (?, ?, ?, 0)");
            return $stmt->execute([$userId, $message, $status]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getByUser($userId, $limit = 5)
    {
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countUnread($userId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

    public function markAllAsRead($userId)
    {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }

    public function getAllForAdmin()
    {
        $sql = "SELECT n.*, u.username, u.email FROM notifications n JOIN users u ON n.user_id = u.id ORDER BY n.created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }
}
