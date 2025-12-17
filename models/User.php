<?php
class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function login($email, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            unset($user['password_hash']);
            return $user;
        }
        return false;
    }

    public function register($username, $email, $password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $role = 'guest user';

        try {
            $stmt = $this->db->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
            return $stmt->execute([$username, $email, $hash, $role]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function create($username, $email, $password, $role)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $this->db->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
            return $stmt->execute([$username, $email, $hash, $role]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT id, username, email, role, lat, lon, profile_photo, last_location_name FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT id, username, email, role, lat, lon FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function updateProfile($id, $username, $email, $photoPath = null)
    {
        $sql = "UPDATE users SET username = ?, email = ?";
        $params = [$username, $email];

        if ($photoPath !== null) {
            $sql .= ", profile_photo = ?";
            $params[] = $photoPath;
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function update($id, $username, $email, $role, $password = null)
    {
        $sql = "UPDATE users SET username = ?, email = ?, role = ?";
        $params = [$username, $email, $role];

        if ($password !== null) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql .= ", password_hash = ?";
            $params[] = $hash;
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteUser($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getAllUsers()
    {
        return $this->db->query("SELECT id, username, email, role, created_at, profile_photo FROM users ORDER BY id ASC")->fetchAll();
    }

    public function updatePasswordByEmail($email, $newPassword)
    {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        try {
            $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
            return $stmt->execute([$hash, $email]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
