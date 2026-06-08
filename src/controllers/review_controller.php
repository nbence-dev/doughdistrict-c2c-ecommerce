<?php
require_once ROOT_PATH . "/models/Review.php";
require_once ROOT_PATH . "/models/Product.php";

$user = current_user();
$reviewModel = new Review($pdo);
$productModel = new Product($pdo);


if ($path === 'reviews/create') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . BASE_URL . 'browse');
        exit();
    }

    $product_id = (int) $_POST['product_id'] ?? 0;
    $order_id = (int) $_POST['order_id'] ?? 0;
    $rating = (int) $_POST['rating'] ?? 0;
    $comment = trim($_POST['comment'] ?? '');
    $comment = $comment === '' ? null : $comment;
    // Validate rating range
    if ($rating < 1 || $rating > 5) {
        set_flash('Please select a rating between 1 and 5.', 'danger');
        header('Location: ' . BASE_URL . 'product?id=' . $product_id);
        exit();
    }

    // Eligibility check: the submitted order_id must be one of the buyer's own
    // delivered, not-yet-reviewed orders for this product. Re-checking here (not
    // just hiding the form) stops a hand-crafted POST from reviewing something
    // the buyer never bought or hasn't received.
    $eligible = $reviewModel->eligibleOrderIds($product_id, $user['id']);
    if (!in_array($order_id, $eligible, true)) {
        set_flash('You are not eligible to review this product for that order.', 'danger');
        header('Location: ' . BASE_URL . 'product?id=' . $product_id);
        exit();
    }

    // Second guard against a double submit slipping two reviews through.
    if ($reviewModel->hasReviewed($product_id, $user['id'], $order_id)) {
        set_flash('You have already reviewed this product for that order.', 'warning');
        header('Location: ' . BASE_URL . 'product?id=' . $product_id);
        exit();
    }

    $reviewModel->create($product_id, $user['id'], $order_id, $rating, $comment);
    set_flash('Review submitted. Thank you!', 'success');
    header('Location: ' . BASE_URL . 'product?id=' . $product_id);
    exit();


}

?>