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

    public function countAllUsers(string $filter = 'all'): int
    {
        if ($filter === 'inactive') {
            return (int) $this->db->query('SELECT COUNT(*) FROM users WHERE is_active = 0')->fetchColumn();
        }
        if (in_array($filter, ['admin', 'seller', 'buyer'])) {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM users WHERE role = ?');
            $stmt->execute([$filter]);
            return (int) $stmt->fetchColumn();
        }
        return (int) $this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }

    public function getPaginated(int $limit, int $offset, string $filter = 'all'): array
    {
        $where = '';
        $params = [];
        if ($filter === 'inactive') {
            $where = 'WHERE is_active = 0';
        } elseif (in_array($filter, ['admin', 'seller', 'buyer'])) {
            $where = 'WHERE role = ?';
            $params[] = $filter;
        }
        $params[] = $limit;
        $params[] = $offset;
        $stmt = $this->db->prepare(
            "SELECT id, name, email, role, is_active, created_at
             FROM users $where ORDER BY created_at DESC LIMIT ? OFFSET ?"
        );
        $stmt->execute($params);
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

    public function invite(string $name, string $email, string $tempPassword): bool
    {
        $hash = password_hash($tempPassword, PASSWORD_DEFAULT);
        try {

            $stmt = $this->db->prepare('INSERT INTO users (name, email, password, role, must_change_password) VALUES (?,?,?,?,1)');
            $stmt->execute([$name, $email, $hash, 'admin']);
            return true;

        } catch (PDOException $e) {
            if ($e->getCode() === 23000)
                return false; // duplicate email
            throw $e;
        }
    }

    public function setPassword(int $id, string $newPassword): void
    {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare('UPDATE users set PASSWORD = ?, must_change_password = 0 WHERE id = ?');
        $stmt->execute([$hash, $id]);
    }
}
?>