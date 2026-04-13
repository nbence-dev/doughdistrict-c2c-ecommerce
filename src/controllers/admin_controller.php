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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === 'admin/users/toggle' && isset($_POST['user_id'])) {
    $userId = (int) $_POST['user_id'];
    $userModel = new User($pdo);
    $user = $userModel->find($userId);
    if ($user) {
        $newStatus = !$user['is_active'];
        $userModel->setActive($userId, $newStatus);
        set_flash("User " . ($newStatus ? "activated" : "deactivated") . " successfully.", 'success');
    } else {
        set_flash("User not found.", 'danger');
    }
    header('Location: ' . BASE_URL . 'admin/users');
    exit();
}
?>