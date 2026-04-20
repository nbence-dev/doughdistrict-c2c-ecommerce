<?php
require_once ROOT_PATH . '/models/Product.php';
require_once ROOT_PATH . '/models/Category.php';

$productModel  = new Product($pdo);
$categoryModel = new Category($pdo);

if ($path === 'browse') {
    $search      = trim($_GET['q'] ?? '');
    $category_id = intval($_GET['category'] ?? 0) ?: null;
    $products    = $productModel->getBrowse($search, $category_id);
    $categories  = $categoryModel->getAll();
}

if ($path === 'product') {
    $id      = intval($_GET['id'] ?? 0);
    $product = $id ? $productModel->findActive($id) : null;
}
