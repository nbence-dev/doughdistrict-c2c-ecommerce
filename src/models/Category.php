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

    // LEFT JOIN so categories with zero products still show up (with count 0)
    // on the admin categories screen.
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

    // Checked before deleting a category so we don't orphan its products.
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

    // Case- and whitespace-insensitive duplicate check so "Bread" and " bread "
    // count as the same name. $excludeId skips the row being edited.
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

    // Turns a display name into a URL-safe slug, e.g. "Cakes & Bakes" -> "cakes-bakes".
    // Lowercases, drops anything that isn't a letter/number/space/dash, then
    // collapses runs of spaces into single dashes.
    private function slugify($name)
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s]+/', '-', $slug);
        return $slug;
    }
}

?>