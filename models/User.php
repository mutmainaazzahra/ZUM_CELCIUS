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
            return $user;
        }
        return false;
    }

    public function register($username, $email, $password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            // Default role: guest user
            $stmt = $this->db->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, 'guest user')");
            return $stmt->execute([$username, $email, $hash]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Fungsi Admin Create User
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

    public function updateProfile($id, $username, $email, $photo = null)
    {
        try {
            if ($photo) {
                $stmt = $this->db->prepare("UPDATE users SET username = ?, email = ?, profile_photo = ? WHERE id = ?");
                return $stmt->execute([$username, $email, $photo, $id]);
            } else {
                $stmt = $this->db->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                return $stmt->execute([$username, $email, $id]);
            }
        } catch (PDOException $e) {
            return false;
        }
    }

    public function update($id, $username, $email, $role, $password = null)
    {
        try {
            if ($password) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $this->db->prepare("UPDATE users SET username=?, email=?, role=?, password_hash=? WHERE id=?");
                return $stmt->execute([$username, $email, $role, $hash, $id]);
            } else {
                $stmt = $this->db->prepare("UPDATE users SET username=?, email=?, role=? WHERE id=?");
                return $stmt->execute([$username, $email, $role, $id]);
            }
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getAllUsers()
    {
        return $this->db->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
    }

    public function deleteUser($id)
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
