<?php
class Category
{
    private $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    public function create($name)
    {
        try {
            $slug = $this->slugify($name);
            $stmt = $this->db->prepare('INSERT INTO categories (name, slug) VALUES (?, ?)');
            return $stmt->execute([$name, $slug]);
        } catch (PDOException $e) {
            throw $e; // Rethrow exceptions
        }
    }

    public function getAll()
    {
        $stmt = $this->db->query('SELECT * FROM categories');
        return $stmt->fetchAll();
    }

    public function getAllWithCount()
    {
        $stmt = $this->db->query('SELECT c.*, COUNT(p.id) AS product_count FROM categories c LEFT JOIN products p ON p.category_id = c.id GROUP BY c.id');
        return $stmt->fetchAll();
    }

    public function find($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id, $name)
    {
        try {
            $slug = $this->slugify($name);
            $stmt = $this->db->prepare('UPDATE categories SET name = ?, slug = ? WHERE id = ?');
            return $stmt->execute([$name, $slug, $id]);
        } catch (PDOException $e) {
            throw $e; // Rethrow exceptions
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare('DELETE FROM categories WHERE id = ?');
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw $e; // Rethrow exceptions
        }
    }

    private function slugify($name)
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s]+/', '-', $slug);
        return $slug;
    }
}

?>