<?php
class PushSubscription
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function saveSubscription($userId, $endpoint, $p256dh, $auth)
    {
        $stmt = $this->db->prepare("SELECT id FROM push_subscriptions WHERE endpoint = ?");
        $stmt->execute([$endpoint]);
        $existing = $stmt->fetch();

        if ($existing) {
            $stmt = $this->db->prepare("UPDATE push_subscriptions SET user_id = ?, p256dh = ?, auth = ? WHERE id = ?");
            return $stmt->execute([$userId, $p256dh, $auth, $existing['id']]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO push_subscriptions (user_id, endpoint, p256dh, auth) VALUES (?, ?, ?, ?)");
            return $stmt->execute([$userId, $endpoint, $p256dh, $auth]);
        }
    }

    public function getByUserId($userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM push_subscriptions WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM push_subscriptions");
        return $stmt->fetchAll();
    }
}
