<?php
class Activity
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getByUser($userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM activities WHERE user_id = ? ORDER BY date DESC, time DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getAllForAdmin()
    {
        return $this->db->query("SELECT a.*, u.username FROM activities a JOIN users u ON a.user_id = u.id ORDER BY a.date DESC")->fetchAll();
    }

    public function create($userId, $name, $type, $notes, $date, $time = '00:00:00')
    {
        $stmt = $this->db->prepare("INSERT INTO activities (user_id, name, type, notes, date, time) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$userId, $name, $type, $notes, $date, $time]);
    }

    public function update($id, $userId, $name, $type, $notes, $date, $time = '00:00:00')
    {
        $stmt = $this->db->prepare("UPDATE activities SET name = ?, type = ?, notes = ?, date = ?, time = ? WHERE id = ? AND user_id = ?");
        return $stmt->execute([$name, $type, $notes, $date, $time, $id, $userId]);
    }

    public function delete($id, $userId)
    {
        $stmt = $this->db->prepare("DELETE FROM activities WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }

    public function getStats()
    {
        return $this->db->query("SELECT type, COUNT(*) as count FROM activities GROUP BY type")->fetchAll();
    }
}
