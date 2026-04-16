<?php
class Product
{
    private $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    public function create($seller_id, $category_id, $name, $description, $price, $stock_qty, $image_url)
    {
        try {
            $stmt = $this->db->prepare('INSERT INTO products (seller_id, category_id, name, description, price, stock_qty, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)');
            return $stmt->execute([$seller_id, $category_id, $name, $description, $price, $stock_qty, $image_url]);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function findBySeller(int $seller_id): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, c.name AS category_name
             FROM products p
             JOIN categories c ON p.category_id = c.id
             WHERE p.seller_id = ?
             ORDER BY p.created_at DESC'
        );
        $stmt->execute([$seller_id]);
        return $stmt->fetchAll();
    }

    public function getAll()
    {
        $stmt = $this->db->query('SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id');
        return $stmt->fetchAll();
    }

    public function countAll(string $filter = 'all'): int
    {
        if (in_array($filter, ['pending', 'active', 'rejected'])) {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM products WHERE status = ?');
            $stmt->execute([$filter]);
            return (int) $stmt->fetchColumn();
        }
        return (int) $this->db->query('SELECT COUNT(*) FROM products')->fetchColumn();
    }

    public function getPaginated(int $limit, int $offset, string $filter = 'all'): array
    {
        $where = '';
        $params = [];
        if (in_array($filter, ['pending', 'active', 'rejected'])) {
            $where = 'WHERE p.status = ?';
            $params[] = $filter;
        }
        $params[] = $limit;
        $params[] = $offset;
        $stmt = $this->db->prepare(
            "SELECT p.*, c.name AS category_name, sp.shop_name, u.name AS seller_name
             FROM products p
             JOIN categories c ON p.category_id = c.id
             JOIN seller_profiles sp ON p.seller_id = sp.id
             JOIN users u ON sp.user_id = u.id
             $where ORDER BY p.created_at DESC LIMIT ? OFFSET ?"
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }


    public function find($id)
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, c.name AS category_name, sp.shop_name, u.name AS seller_name
             FROM products p
             JOIN categories c ON p.category_id = c.id
             JOIN seller_profiles sp ON p.seller_id = sp.id
             JOIN users u ON sp.user_id = u.id
             WHERE p.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id, $category_id, $name, $description, $price, $stock_qty, $image_url)
    {
        try {
            $stmt = $this->db->prepare('UPDATE products SET category_id = ?, name = ?, description = ?, price = ?, stock_qty = ?, image_url = ? WHERE id = ?');
            return $stmt->execute([$category_id, $name, $description, $price, $stock_qty, $image_url, $id]);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare('DELETE FROM products WHERE id = ?');
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw $e; // Rethrow exceptions
        }
    }

    public function setStatus($id, $status)
    {
        $allowed = ['pending', 'active', 'rejected'];
        if (!in_array($status, $allowed))
            return false;
        $stmt = $this->db->prepare('UPDATE products SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }

    // admin: all products with seller name, shop name, and category
    public function getAllWithSeller()
    {
        $stmt = $this->db->query(
            'SELECT p.*, c.name AS category_name, sp.shop_name, u.name AS seller_name
             FROM products p
             JOIN categories c ON p.category_id = c.id
             JOIN seller_profiles sp ON p.seller_id = sp.id
             JOIN users u ON sp.user_id = u.id
             ORDER BY p.created_at DESC'
        );
        return $stmt->fetchAll();
    }




}
?>