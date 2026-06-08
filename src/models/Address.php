<?php
class Address
{
    private $db;
    public function __construct($pdo)
    {
        $this->db = $pdo;

    }
    // Default address sorts to the top (is_default DESC) so it's pre-selected
    // at checkout, then the rest oldest-first.
    public function findByUser($user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC, created_at ASC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function create($user_id, $label, $street, $local_area, $city, $province, $postal_code)
    {
        $stmt = $this->db->prepare("INSERT INTO addresses (user_id, label, street, local_area, city, province, postal_code) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $label, $street, $local_area ?: null, $city, $province, $postal_code]);
        return $this->db->lastInsertId();
    }

    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM addresses WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

}
?>