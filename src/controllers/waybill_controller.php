<?php
require_once ROOT_PATH . '/models/Order.php';
require_once ROOT_PATH . '/models/SellerProfile.php';
require_once ROOT_PATH . '/helpers/courier.php';

$user               = current_user();
$orderModel         = new Order($pdo);
$sellerProfileModel = new SellerProfile($pdo);
$sellerProfile      = $sellerProfileModel->findByUserId($user['id']);

$order_id = (int) ($_GET['id'] ?? 0);
$data     = $orderModel->findById($order_id);
$order    = $data['order'] ?? null;

if (!$order || !$sellerProfile || (int) $order['seller_id'] !== (int) $sellerProfile['id']) {
    set_flash('Order not found.', 'danger');
    header('Location: ' . BASE_URL . 'seller/orders');
    exit();
}

if (empty($order['shiplogic_shipment_id'])) {
    set_flash('No shipment has been booked for this order yet.', 'warning');
    header('Location: ' . BASE_URL . 'seller/orders/detail?id=' . $order_id);
    exit();
}

try {
    $url = shiplogic_get_label_url($order['shiplogic_shipment_id']);
    if (!$url) {
        throw new RuntimeException('Shiplogic did not return a label URL.');
    }
    header('Location: ' . $url);
    exit();
} catch (RuntimeException $e) {
    set_flash('Could not fetch waybill: ' . $e->getMessage(), 'danger');
    header('Location: ' . BASE_URL . 'seller/orders/detail?id=' . $order_id);
    exit();
}
