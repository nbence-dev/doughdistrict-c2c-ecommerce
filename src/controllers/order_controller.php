<?php
/** @var string $path injected by index.php before require_once */
require_once ROOT_PATH . '/models/Order.php';
require_once ROOT_PATH . '/models/SellerProfile.php';
require_once ROOT_PATH . '/models/User.php';
require_once ROOT_PATH . '/helpers/emails.php';

$user = current_user();
$order = new Order($pdo);

if ($path === 'orders') {
    $orders = $order->findByBuyer($user['id']);

} elseif ($path === 'orders/detail') {
    $id = (int) ($_GET['id'] ?? 0);
    $data = $order->findById($id);

    // Ownership check: a buyer can only open their own order, so they can't
    // read someone else's by changing the id in the URL.
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

    $sellerProfileModel = new SellerProfile($pdo);
    $sellerProfile = $sellerProfileModel->findByUserId($user['id']);

    // Same idea on the seller side: only the seller who owns the order may view
    // or update it. Compares the order's seller_id to this seller's profile id.
    if (!$data['order'] || !$sellerProfile || (int) $data['order']['seller_id'] !== (int) $sellerProfile['id']) {
        set_flash('Forbidden', 'danger');
        header('Location: ' . BASE_URL . 'seller/dashboard');
        exit();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $newStatus = $_POST['status'] ?? '';

        // Move the order to the seller-chosen status. When it reaches 'delivered'
        // we email the buyer so they know to expect/collect it.
        $order->updateStatus($id, $newStatus);
        if ($newStatus === 'delivered') {
            $userModel = new User($pdo);
            $buyerUser = $userModel->find($data['order']['buyer_id']);
            if ($buyerUser) {
                email_order_delivered($buyerUser['email'], $buyerUser['name'], $id, $data['items']);
            }
        }
        set_flash('Status successfully updated', 'success');
        header('Location: ' . BASE_URL . 'seller/orders/detail?id=' . $id);
        exit();
    }
}
?>