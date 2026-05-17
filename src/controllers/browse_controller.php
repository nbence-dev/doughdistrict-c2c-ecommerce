<?php
require_once ROOT_PATH . '/models/Product.php';
require_once ROOT_PATH . '/models/Category.php';

$productModel = new Product($pdo);
$categoryModel = new Category($pdo);

if ($path === 'browse') {
    $search        = trim($_GET['q'] ?? '');
    $category_slug = trim($_GET['category'] ?? '');
    $category_id   = null;
    if ($category_slug) {
        $resolved    = $categoryModel->findBySlug($category_slug);
        $category_id = $resolved ? (int) $resolved['id'] : null;
    }
    $products   = $productModel->getBrowse($search, $category_id);
    $categories = $categoryModel->getAll();
}

if ($path === 'product') {
    $id = intval($_GET['id'] ?? 0);
    $product = $id ? $productModel->findActive($id) : null;

    if ($product) {
        require_once ROOT_PATH . '/models/Review.php';
        $reviewModel = new Review($pdo);
        $reviews = $reviewModel->forProduct($id);
        $avgRating = $reviewModel->avgRating($id);
        $reviewCount = $reviewModel->countForProduct($id);
        $eligibleOrderIds = (current_user()['role'] !== 'admin')
            ? $reviewModel->eligibleOrderIds($id, current_user()['id'])
            : [];
    }
}
