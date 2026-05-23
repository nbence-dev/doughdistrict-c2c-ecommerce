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
        $slug = $this->slugify($name);
        $stmt = $this->db->prepare('INSERT INTO categories (name, slug) VALUES (?, ?)');
        return $stmt->execute([$name, $slug]);
    }

    public function getAll()
    {
        $stmt = $this->db->query('SELECT * FROM categories ORDER BY name');
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
        $slug = $this->slugify($name);
        $stmt = $this->db->prepare('UPDATE categories SET name = ?, slug = ? WHERE id = ?');
        return $stmt->execute([$name, $slug, $id]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare('DELETE FROM categories WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function hasProducts(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM products WHERE category_id = ?');
        $stmt->execute([$id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE slug = ?');
        $stmt->execute([$slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function nameExists($name, $excludeId = null)
    {
        if ($excludeId) {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM categories WHERE LOWER(TRIM(name)) = LOWER(TRIM(?)) AND id != ?');
            $stmt->execute([$name, $excludeId]);
        } else {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM categories WHERE LOWER(TRIM(name)) = LOWER(TRIM(?))');
            $stmt->execute([$name]);
        }
        return (int) $stmt->fetchColumn() > 0;
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