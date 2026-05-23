<?php
require_once ROOT_PATH . '/models/Product.php';
require_once ROOT_PATH . '/models/Category.php';
require_once ROOT_PATH . '/helpers/emails.php';

// Views for each page (users, products, categories) will be included in the main admin view
if ($path === 'admin/users') {
    $userModel = new User($pdo);

    $allowedFilters = ['all', 'admin', 'seller', 'buyer', 'inactive'];
    $filter = in_array($_GET['filter'] ?? '', $allowedFilters) ? $_GET['filter'] : 'all';

    $perPage = 15;
    $page = max(1, (int) ($_GET['page'] ?? 1));
    $total = $userModel->countAllUsers($filter);
    $totalPages = (int) ceil($total / $perPage);
    $page = min($page, max(1, $totalPages));
    $users = $userModel->getPaginated($perPage, ($page - 1) * $perPage, $filter);

} elseif ($path === 'admin/products') {
    $productModel = new Product($pdo);

    $allowedFilters = ['all', 'pending', 'active', 'rejected'];
    $filter = in_array($_GET['filter'] ?? '', $allowedFilters) ? $_GET['filter'] : 'all';

    $perPage = 15;
    $page = max(1, (int) ($_GET['page'] ?? 1));
    $total = $productModel->countAll($filter);
    $totalPages = (int) ceil($total / $perPage);
    $page = min($page, max(1, $totalPages));
    $products = $productModel->getPaginated($perPage, ($page - 1) * $perPage, $filter);


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
        if ($userId === current_user()['id']) {
            set_flash("You cannot deactivate your own account.", 'danger');
            header('Location: ' . BASE_URL . 'admin/users');
            exit();
        }
        $newStatus = !$user['is_active'];
        $userModel->setActive($userId, $newStatus);
        set_flash("User " . ($newStatus ? "activated" : "deactivated") . " successfully.", 'success');
    } else {
        set_flash("User not found.", 'danger');
    }
    header('Location: ' . BASE_URL . 'admin/users');
    exit();
}
// Role changes via the admin panel are disabled — admins are created by invite only.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === 'admin/users/role') {
    set_flash("Role changes are not permitted. Admins are created by invite only.", 'danger');
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
    if ($categoryModel->hasProducts($categoryId)) {
        set_flash("Cannot delete category with associated products. Please reassign or delete those products first.", 'danger');
    } else {
        $categoryModel->delete($categoryId);
        set_flash("Category deleted successfully.", 'success');
    }

    header('Location: ' . BASE_URL . 'admin/categories');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === 'admin/users/invite') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_flash('Please provide a valid name and email.', 'danger');
        header('Location: ' . BASE_URL . 'admin/users');
        exit();
    }

    $tempPassword = strtoupper(bin2hex(random_bytes(4))) . strtolower(bin2hex(random_bytes(3)));

    $userModel = new User($pdo);
    $created = $userModel->invite($name, $email, $tempPassword);

    if (!$created) {
        set_flash('That email address is already registered.', 'danger');
        header('Location: ' . BASE_URL . 'admin/users');
        exit();
    }

    email_admin_invite($email, $name, $tempPassword);
    set_flash('Invitation sent to ' . htmlspecialchars($email) . '.', 'success');
    header('Location: ' . BASE_URL . 'admin/users');
    exit();

}

?>