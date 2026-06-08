<?php
class Order
{
    private $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    // Creates one order row per seller. The shipping_* columns store a snapshot
    // of the buyer's address at purchase time, so later edits to their saved
    // address book don't change what was on a past order. Status starts at 'paid'
    // because the order is only written after Stripe confirms payment.
    public function create($buyer_id, $seller_id, $total_amount, $stripe_payment_intent_id, $shipping, $shipping_cost = null)
    {
        $stmt = $this->db->prepare("INSERT INTO orders
        (buyer_id, seller_id, status, total_amount, stripe_payment_intent_id, shipping_cost, shipping_name, shipping_street, shipping_city, shipping_province, shipping_postal_code)
        VALUES (?, ?, 'paid', ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$buyer_id, $seller_id, $total_amount, $stripe_payment_intent_id, $shipping_cost, $shipping['name'], $shipping['street'], $shipping['city'], $shipping['province'], $shipping['postal_code']]);
        return $this->db->lastInsertId();
    }

    // Saves the line items. product_name and unit_price are copied in as a
    // snapshot so the order still reads correctly if the product is later
    // renamed, repriced, or deleted.
    public function createItems($order_id, array $items)
    {
        $stmt = $this->db->prepare("
        INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity)
        VALUES (?, ?, ?, ?, ?)
        ");
        foreach ($items as $item) {
            $stmt->execute([$order_id, $item['product_id'], $item['product_name'], $item['unit_price'], $item['quantity']]);
        }
    }

    public function findByBuyer($buyer_id)
    {
        $stmt = $this->db->prepare('
        SELECT o.*, sp.shop_name
        FROM orders o
        JOIN seller_profiles sp ON sp.id = o.seller_id
        WHERE o.buyer_id = ?
        ORDER BY o.created_at DESC');
        $stmt->execute([$buyer_id]);
        return $stmt->fetchAll();
    }

    public function updateStatus($order_id, $status)
    {
        $stmt = $this->db->prepare('UPDATE orders SET status = ? WHERE id = ?');
        $stmt->execute([$status, $order_id]);
    }

    // Called after a shipment is booked with the courier. Saves the tracking
    // details and bumps the order to 'shipped' in the same update.
    public function storeTracking($order_id, $shiplogic_shipment_id, $tracking_reference, $estimated_collection = null)
    {
        $stmt = $this->db->prepare('UPDATE orders SET shiplogic_shipment_id = ?, tracking_reference = ?, estimated_collection = ?, status = ? WHERE id = ?');
        $stmt->execute([$shiplogic_shipment_id, $tracking_reference, $estimated_collection, 'shipped', $order_id]);
    }

    // Returns the order header plus its line items in one call, so order detail
    // pages get everything they need as ['order' => ..., 'items' => ...].
    public function findById($order_id)
    {
        $stmt = $this->db->prepare('SELECT o.*, sp.shop_name FROM orders o
        JOIN seller_profiles sp ON sp.id = o.seller_id
        WHERE o.id = ?');
        $stmt->execute([$order_id]);
        $order = $stmt->fetch();

        $stmt2 = $this->db->prepare('SELECT * FROM order_items WHERE order_id = ?');
        $stmt2->execute([$order_id]);
        $items = $stmt2->fetchAll();

        return ['order' => $order, 'items' => $items];
    }

    // Seller's order list. Note $seller_id here is the user id, joined through
    // seller_profiles. The FIELD() sort forces the workflow order (unfulfilled
    // 'paid' orders first, 'delivered' last) rather than plain alphabetical,
    // then newest-first within each status group.
    public function findBySeller($seller_id)
    {
        $stmt = $this->db->prepare('SELECT o.*, u.name
        FROM orders o
        JOIN users u ON u.id = o.buyer_id
        JOIN seller_profiles sp ON sp.id = o.seller_id
        WHERE sp.user_id = ?
        ORDER BY
            FIELD(o.status, \'paid\', \'processing\', \'shipped\', \'delivered\') ASC,
            o.created_at DESC');
        $stmt->execute([$seller_id]);
        return $stmt->fetchAll();
    }

}
?>
