<?php
class Product
{
    private $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    public function create($seller_id, $category_id, $name, $description, $price, $stock_qty, $image_url, $weight_kg = 0, $length_cm = 0, $width_cm = 0, $height_cm = 0, $shipping_cost = null)
    {
        $stmt = $this->db->prepare('INSERT INTO products (seller_id, category_id, name, description, price, stock_qty, image_url, weight_kg, length_cm, width_cm, height_cm, shipping_cost) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        return $stmt->execute([$seller_id, $category_id, $name, $description, $price, $stock_qty, $image_url, $weight_kg, $length_cm, $width_cm, $height_cm, $shipping_cost]);
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

    public function update($id, $category_id, $name, $description, $price, $stock_qty, $image_url, $weight_kg = 0, $length_cm = 0, $width_cm = 0, $height_cm = 0, $shipping_cost = null)
    {
        $stmt = $this->db->prepare('UPDATE products SET category_id = ?, name = ?, description = ?, price = ?, stock_qty = ?, image_url = ?, weight_kg = ?, length_cm = ?, width_cm = ?, height_cm = ?, shipping_cost = ? WHERE id = ?');
        return $stmt->execute([$category_id, $name, $description, $price, $stock_qty, $image_url, $weight_kg, $length_cm, $width_cm, $height_cm, $shipping_cost, $id]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare('DELETE FROM products WHERE id = ?');
        return $stmt->execute([$id]);
    }

    // Used by admin product moderation. The allowlist guards against an
    // arbitrary status being written even though the column is an ENUM.
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

    // Public storefront listing. Only 'active' products are ever shown to buyers;
    // search and category filters are optional and stacked on top with AND.
    public function getBrowse($search = '', $category_id = null)
    {
        // Start with the always-on active filter, then add optional conditions.
        $conditions = ["p.status = 'active'"];
        $params = [];
        if (!empty($search)) {
            // Match the keyword against either the name or the description.
            $conditions[] = '(p.name LIKE ? OR p.description LIKE ?)';
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($category_id) {
            $conditions[] = 'p.category_id = ?';
            $params[] = $category_id;
        }
        $where = 'WHERE ' . implode(' AND ', $conditions);

        // The two subqueries pull the average rating and review count per product
        // so the browse cards can show stars without a second round-trip.
        $stmt = $this->db->prepare(
            "SELECT p.*, c.name AS category_name, sp.shop_name, u.name AS seller_name,
                    (SELECT AVG(r.rating) FROM reviews r WHERE r.product_id = p.id) AS avg_rating,
                    (SELECT COUNT(*) FROM reviews r WHERE r.product_id = p.id) AS review_count
             FROM products p
             JOIN categories c ON p.category_id = c.id
             JOIN seller_profiles sp ON p.seller_id = sp.id
             JOIN users u ON sp.user_id = u.id
             $where ORDER BY p.created_at DESC"
        );

        $stmt->execute($params);
        return $stmt->fetchAll();

    }

    public function findActive($id)
    {
        $stmt = $this->db->prepare('SELECT p.*, c.name AS category_name, c.slug AS category_slug, sp.shop_name, u.name AS seller_name, u.id AS seller_user_id
             FROM products p
             JOIN categories c ON p.category_id = c.id
             JOIN seller_profiles sp ON p.seller_id = sp.id
             JOIN users u ON sp.user_id = u.id
             WHERE p.id = ? AND p.status = "active"');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

}
?>