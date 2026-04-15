<?php
require_once ROOT_PATH . '/models/Product.php';
require_once ROOT_PATH . '/models/Category.php';

// Views for each page (users, products, categories) will be included in the main admin view
if ($path === 'admin/users') {
    //fetch users
    $userModel = new User($pdo);

    $perPage = 5;
    $page = max(1, (int) ($_GET['page'] ?? 1));
    $total = $userModel->countAllUsers();
    $totalPages = (int) ceil($total / $perPage);
    $page = min($page, max(1, $totalPages));   // clamp to valid range
    $users = $userModel->getPaginated($perPage, ($page - 1) * $perPage);

} elseif ($path === 'admin/products') {
    //fetch products
    $productModel = new Product($pdo);
    $perPage = 5;
    $page = max(1, (int) ($_GET['page'] ?? 1));
    $total = $productModel->countAll();
    $totalPages = (int) ceil($total / $perPage);
    $page = min($page, max(1, $totalPages));
    $products = $productModel->getPaginated($perPage, ($page - 1) * $perPage);


} else if ($path === 'admin/categories') {
    //fetch categories
    $categoryModel = new Category($pdo);
    $categories = $categoryModel->getAllWithCount();
    $productModel = new Product($pdo);
    $totalProducts = $productModel->countAll();

}


// Handle POST requests for user status toggle
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
// Handle POST requests for user role change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === 'admin/users/role' && isset($_POST['user_id'], $_POST['role'])) {
    $userId = (int) $_POST['user_id'];
    $newRole = $_POST['role'];
    $userModel = new User($pdo);
    $user = $userModel->find($userId);
    if ($user) {
        $allowed = ['buyer' => 'admin', 'admin' => 'buyer'];
        if (!array_key_exists($user['role'], $allowed) || $allowed[$user['role']] !== $newRole) {
            set_flash("Role change not permitted.", 'danger');
            header('Location: ' . BASE_URL . 'admin/users');
            exit();
        }
        $userModel->setRole($userId, $newRole);
        set_flash("User role updated to " . htmlspecialchars($newRole) . " successfully.", 'success');
    } else {
        set_flash("User not found.", 'danger');
    }
    header('Location: ' . BASE_URL . 'admin/users');
    exit();
}
// Handle POST requests for product status change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === 'admin/products/status' && isset($_POST['product_id'], $_POST['status'])) {
    $productId = (int) $_POST['product_id'];
    $newStatus = $_POST['status'];
    $productModel = new Product($pdo);
    if ($productModel->setStatus($productId, $newStatus)) {
        $label = ['pending' => 'marked as pending', 'active' => 'approved', 'rejected' => 'rejected'];
        set_flash("Product " . ($label[$newStatus] ?? 'updated') . " successfully.", 'success');
    } else {
        set_flash("Invalid status or product not found.", 'danger');
    }
    header('Location: ' . BASE_URL . 'admin/products');
    exit();
}
// Handle POST requests for category management
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === 'admin/categories/create' && isset($_POST['name'])) {
    $name = trim($_POST['name']);
    if (!empty($name)) {
        $categoryModel = new Category($pdo);
        if ($categoryModel->nameExists($name)) {
            set_flash("A category named '" . htmlspecialchars($name) . "' already exists.", 'danger');
        } else {
            $categoryModel->create($name);
            set_flash("Category '" . htmlspecialchars($name) . "' created successfully.", 'success');
        }
    } else {
        set_flash("Category name cannot be empty.", 'danger');
    }
    header('Location: ' . BASE_URL . 'admin/categories');
    exit();
}
// Handle POST requests for category update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === 'admin/categories/update' && isset($_POST['category_id'], $_POST['name'])) {
    $categoryId = (int) $_POST['category_id'];
    $name = trim($_POST['name']);
    if (!empty($name)) {
        $categoryModel = new Category($pdo);
        if ($categoryModel->nameExists($name, $categoryId)) {
            set_flash("A category named '" . htmlspecialchars($name) . "' already exists.", 'danger');
        } else {
            $categoryModel->update($categoryId, $name);
            set_flash("Category updated successfully.", 'success');
        }
    } else {
        set_flash("Category name cannot be empty.", 'danger');
    }
    header('Location: ' . BASE_URL . 'admin/categories');
    exit();
}
// Handle POST requests for category deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === 'admin/categories/delete' && isset($_POST['category_id'])) {
    $categoryId = (int) $_POST['category_id'];
    $categoryModel = new Category($pdo);
    $categoryModel->delete($categoryId);
    set_flash("Category deleted successfully.", 'success');
    header('Location: ' . BASE_URL . 'admin/categories');
    exit();
}


?>