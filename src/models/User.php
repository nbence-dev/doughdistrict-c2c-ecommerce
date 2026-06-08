<?php
class User
{
    private $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    // Register new user
    public function create($name, $email, $password, $role = 'buyer', $phone_number = null)
    {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $this->db->prepare('INSERT INTO users (name, email, password, role, phone_number) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$name, $email, $hashed_password, $role, $phone_number ?: null]);
            return true;
        } catch (PDOException $e) {
            // SQLSTATE 23000 is a unique-key violation, which here means the email
            // is already registered. Return false so the controller can show a
            // friendly message instead of crashing on the duplicate.
            if ($e->getCode() == 23000) {
                return false;
            }
            throw $e;
        }
    }

    // Used at login. The is_active = 1 filter means deactivated accounts can't
    // be found, so they're locked out without needing a separate password check.
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

    // Total count for the admin user list pagination. The $filter lets the
    // admin page count only one role, or only deactivated accounts.
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

    // Returns one page of users for the admin table. Builds the WHERE clause to
    // match the same filter used by countAllUsers so the two stay in sync.
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
        // limit/offset are appended last so they line up with the trailing ?s
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

    // Admins can only be created by invite (never through the users panel).
    // must_change_password is set to 1 so the invited admin is forced to pick
    // their own password on first login instead of keeping the temp one.
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

    // Clears must_change_password too, so the forced-change flow only runs once.
    public function setPassword(int $id, string $newPassword): void
    {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare('UPDATE users SET password = ?, must_change_password = 0 WHERE id = ?');
        $stmt->execute([$hash, $id]);
    }

    // Returns false if the email is already taken by another account.
    public function updateProfile(int $id, string $name, string $email): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
        $stmt->execute([$email, $id]);
        if ($stmt->fetch()) {
            return false;
        }
        $stmt = $this->db->prepare('UPDATE users SET name = ?, email = ? WHERE id = ?');
        $stmt->execute([$name, $email, $id]);
        return true;
    }
}
?>