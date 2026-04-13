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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === 'admin/users/role' && isset($_POST['user_id'], $_POST['role'])) {
    $userId = (int) $_POST['user_id'];
    $newRole = $_POST['role'];
    $userModel = new User($pdo);
    $user = $userModel->find($userId);
    if ($user) {
        $userModel->setRole($userId, $newRole);
        set_flash("User role updated to " . htmlspecialchars($newRole) . " successfully.", 'success');
    } else {
        set_flash("User not found.", 'danger');
    }
    header('Location: ' . BASE_URL . 'admin/users');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === 'admin/products/toggle' && isset($_POST['product_id'])) {
    $productId = (int) $_POST['product_id'];
    $productModel = new Product($pdo);
    $product = $productModel->find($productId);
    if ($product) {
        $newStatus = !$product['is_active'];
        $productModel->setActive($productId, $newStatus);
        set_flash("Product " . ($newStatus ? "activated" : "deactivated") . " successfully.", 'success');
    } else {
        set_flash("Product not found.", 'danger');
    }
    header('Location: ' . BASE_URL . 'admin/products');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === 'admin/categories/create' && isset($_POST['name'])) {
    $name = trim($_POST['name']);
    if (!empty($name)) {
        $categoryModel = new Category($pdo);
        $categoryModel->create($name);
        set_flash("Category '" . htmlspecialchars($name) . "' created successfully.", 'success');
    } else {
        set_flash("Category name cannot be empty.", 'danger');
    }
    header('Location: ' . BASE_URL . 'admin/categories');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === 'admin/categories/update' && isset($_POST['category_id'], $_POST['name'])) {
    $categoryId = (int) $_POST['category_id'];
    $name = trim($_POST['name']);
    if (!empty($name)) {
        $categoryModel = new Category($pdo);
        $categoryModel->update($categoryId, $name);
        set_flash("Category updated successfully.", 'success');
    } else {
        set_flash("Category name cannot be empty.", 'danger');
    }
    header('Location: ' . BASE_URL . 'admin/categories');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === 'admin/categories/delete' && isset($_POST['category_id'])) {
    $categoryId = (int) $_POST['category_id'];
    $categoryModel = new Category($pdo);
    $categoryModel->delete($categoryId);
    set_flash("Category deleted successfully.", 'success');
    header('Location: ' . BASE_URL . 'admin/categories');
    exit();
}
?>