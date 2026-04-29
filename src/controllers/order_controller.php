<?php
/** @var string $path injected by index.php before require_once */
require_once ROOT_PATH . '/models/Order.php';

$user = current_user();
$order = new Order($pdo);

if ($path === 'orders') {
    $orders = $order->findByBuyer($user['id']);

} elseif ($path === 'orders/detail') {
    $id = (int) ($_GET['id'] ?? 0);
    $data = $order->findById($id);

    if (!$data['order'] || $data['order']['buyer_id'] !== $user['id']) {
        set_flash('Forbidden', 'danger');
        header('Location: ' . BASE_URL . 'browse');
        exit();
    }





} elseif ($path === 'seller/orders') {
    $orders = $order->findBySeller($user['id']);

} elseif ($path === 'seller/orders/detail') {
    $id = (int) ($_GET['id'] ?? 0);
    $data = $order->findById($id);

    if (!$data['order'] || $data['order']['seller_id'] != $user['id']) {
        set_flash('Forbidden', 'danger');
        header('Location: ' . BASE_URL . 'seller/dashboard');
        exit();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $order->updateStatus($id, $_POST['status']);
        set_flash('Status successfully updated', 'success');
        header('Location: ' . BASE_URL . 'seller/orders/detail?id=' . $id);
        exit();
    }
}
?>