<?php
class Product
{
    private $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    public function create($seller_id, $category_id, $name, $description, $price, $image_url)
    {
        try {
            $stmt = $this->db->prepare('INSERT INTO products (seller_id, category_id, name, description, price, image_url) VALUES (?, ?, ?, ?, ?, ?)');
            return $stmt->execute([$seller_id, $category_id, $name, $description, $price, $image_url]);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getAll()
    {
        $stmt = $this->db->query('SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id');
        return $stmt->fetchAll();
    }

    public function countAll()
    {
        $stmt = $this->db->query('SELECT COUNT(*) FROM products');
        return $stmt->fetchColumn();
    }
    public function getPaginated(int $limit, int $offset): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, c.name AS category_name, sp.shop_name, u.name AS seller_name
           FROM products p                                                                                                                                 
           JOIN categories c ON p.category_id = c.id
           JOIN seller_profiles sp ON p.seller_id = sp.id                                                                                                  
           JOIN users u ON sp.user_id = u.id
           ORDER BY p.created_at DESC                                                                                                                      
           LIMIT ? OFFSET ?'
        );
        $stmt->execute([$limit, $offset]);
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

    public function update($id, $name, $description, $price, $image_url)
    {
        try {
            $stmt = $this->db->prepare('UPDATE products SET name = ?, description = ?, price = ?, image_url = ? WHERE id = ?');
            return $stmt->execute([$name, $description, $price, $image_url, $id]);
        } catch (PDOException $e) {
            throw $e; // Rethrow exceptions
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