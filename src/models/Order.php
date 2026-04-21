<?php
class Order
{
    private $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    public function create($buyer_id, $seller_id, $total_amount, $stripe_payment_intent_id, $shipping)
    {
        $stmt = $this->db->prepare("INSERT INTO orders
        (buyer_id, seller_id, status ,total_amount, stripe_payment_intent_id, shipping_name, shipping_street, shipping_city, shipping_province, shipping_postal_code)
        VALUES (?, ?, 'paid', ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$buyer_id, $seller_id, $total_amount, $stripe_payment_intent_id, $shipping['name'], $shipping['street'], $shipping['city'], $shipping['province'], $shipping['postal_code']]);
        return $this->db->lastInsertId();
    }

    public function createItems($order_id, array $items)
    {
        $stmt = $this->db->prepare("
        INSERT INTO order_items (order_id,product_id, product_name, unit_price, quantity)
        VALUES (?, ?, ?, ?, ?)
        ");
        foreach ($items as $item) {
            $stmt->execute([$order_id, $item['product_id'], $item['product_name'], $item['unit_price'], $item['quantity']]);
        }
    }

    public function findByBuyer($buyer_id)
    {
        $stmt = $this->db->prepare('
        SELECT o.*, sp.shop_Name
        FROM orders o
        JOIN seller_profiles sp ON sp.id = o.seller_id
        WHERE o.buyer_id = ?
        ORDER BY o.created_at DESC');
        $stmt->execute([$buyer_id]);
        return $stmt->fetchAll();
    }

}
?>