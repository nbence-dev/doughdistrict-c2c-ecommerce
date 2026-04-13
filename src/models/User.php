<?php
class User
{
    private $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    // Register new user
    public function create($name, $email, $password, $role = 'buyer')
    {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $this->db->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $email, $hashed_password, $role]);
            return true;
        } catch (PDOException $e) {
            // Handle duplicate email
            if ($e->getCode() == 23000) {
                return false; // User already exists
            }
            throw $e; // Rethrow other exceptions
        }
    }

    public function findByEmail($email)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? AND is_active = 1');
        $stmt->execute([$email]);
        return $stmt->fetch();

    }

    public function find($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }


    // admin functions
    public function getAllUsers()
    {
        $stmt = $this->db->query('SELECT id, name, email, role, is_active, created_at FROM users');
        return $stmt->fetchAll();
    }

    public function setActive($id, $active)
    {
        $stmt = $this->db->prepare('UPDATE users SET is_active = ? WHERE id = ?');
        return $stmt->execute([(int) $active, $id]);
    }

    public function setRole($user_id, $role)
    {
        $stmt = $this->db->prepare('UPDATE users SET role = ? WHERE id = ?');
        return $stmt->execute([$role, $user_id]);
    }
}
?>