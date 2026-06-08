<?php
class Review
{
    private $db;
    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    public function create(int $product_id, int $buyer_id, int $order_id, int $rating, ?string $comment): bool
    {
        $stmt = $this->db->prepare('INSERT INTO reviews (product_id, buyer_id, order_id, rating, comment)
        VALUES (?,?,?,?,?)');
        return $stmt->execute([$product_id, $buyer_id, $order_id, $rating, $comment]);
    }

    public function forProduct(int $product_id): array
    {
        $stmt = $this->db->prepare('SELECT r.rating, r.comment, r.created_at, u.name AS reviewer_name
        FROM reviews r
        JOIN users u ON u.id = r.buyer_id
        WHERE r.product_id = ?
        ORDER BY r.created_at DESC');
        $stmt->execute([$product_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function avgRating(int $product_id): ?float
    {
        $stmt = $this->db->prepare('SELECT AVG(rating) FROM reviews WHERE product_id = ?');
        $stmt->execute([$product_id]);
        $avg = $stmt->fetchColumn();
        return $avg !== null ? (float) $avg : null;
    }

    public function countForProduct(int $product_id): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM reviews WHERE product_id = ?');
        $stmt->execute([$product_id]);
        return (int) $stmt->fetchColumn();
    }

    // Works out which of the buyer's orders let them review this product.
    // An order qualifies only if it contains the product, belongs to the buyer,
    // is already 'delivered', and hasn't been reviewed yet (the NOT EXISTS).
    // This enforces the "one review per buyer per product per order" rule and
    // stops reviews on items that never arrived.
    public function eligibleOrderIds(int $product_id, int $buyer_id): array
    {
        $stmt = $this->db->prepare("SELECT DISTINCT o.id
          FROM orders o
          JOIN order_items oi ON oi.order_id = o.id
          WHERE oi.product_id = ?                                                                                                        
            AND o.buyer_id = ?
            AND o.status = 'delivered'                                                                                                   
            AND NOT EXISTS (
                SELECT 1 FROM reviews r
                WHERE r.product_id = ? AND r.buyer_id = ? AND r.order_id = o.id                                                          
            )");
        $stmt->execute([$product_id, $buyer_id, $product_id, $buyer_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Final guard before insert, in case the same review form is submitted twice.
    public function hasReviewed(int $product_id, int $buyer_id, int $order_id): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM reviews
        WHERE product_id = ? AND buyer_id = ? AND order_id = ?");
        $stmt->execute([$product_id, $buyer_id, $order_id]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
?>