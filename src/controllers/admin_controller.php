<?php
require_once ROOT_PATH . '/models/Product.php';
require_once ROOT_PATH . '/models/Category.php';


if ($path === 'admin/users') {
    //fetch users
    $userModel = new User($pdo);
    $users = $userModel->getAllUsers();

} elseif ($path === 'admin/products') {
    //fetch products
    $productModel = new Product($pdo);
    $products = $productModel->getAllWithSeller();

} else if ($path === 'admin/categories') {
    //fetch categories
    $categoryModel = new Category($pdo);
    $categories = $categoryModel->getAllWithCount();
    $productModel = new Product($pdo);
    $totalProducts = $productModel->countAll();

}
?>