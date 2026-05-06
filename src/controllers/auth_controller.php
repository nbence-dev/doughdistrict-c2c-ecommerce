<?php
// Register

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['register'])) {

    $name = trim($_POST['name']) ?? '';
    $email = trim($_POST['email']) ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // validdation checks

    $error = null;

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    }

    // handle validation failure
    if ($error) {
        set_flash($error, 'danger');
        header('Location: ' . BASE_URL . 'register');
        exit();
    }

    // validation passed, create user
    $userModel = new User($pdo);
    $created = $userModel->create($name, $email, $password);
    if ($created) {
        set_flash("Registration successful! Please log in.", 'success');
        header('Location: ' . BASE_URL . 'login');
        exit();
    } else {
        set_flash("Email already registered.", 'danger');
        header('Location: ' . BASE_URL . 'register');
        exit();
    }
}
// Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']) ?? '';
    $password = $_POST['password'] ?? '';

    $error = null;

    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    }

    if ($error) {
        set_flash($error, 'danger');
        header('Location: ' . BASE_URL . 'login');
        exit();
    }

    $userModel = new User($pdo);
    $user = $userModel->findByEmail($email);

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];

        if ($user['must_change_password']) {
            set_flash('Please set a new password to continue.', 'warning');
            header('Location: ' . BASE_URL . 'account/change-password');
            exit();
        }

        set_flash("Welcome back, " . htmlspecialchars($user['name']) . "!", 'success');
        if ($user['role'] === 'admin') {
            header('Location: ' . BASE_URL . 'admin/users');
        } elseif ($user['role'] === 'seller') {
            header('Location: ' . BASE_URL . 'seller/dashboard');
        } else {
            header('Location: ' . BASE_URL . 'browse');
        }
        exit();
    } else {
        set_flash("Invalid email or password.", 'danger');
        header('Location: ' . BASE_URL . 'login');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (strlen($new) < 8) {
        set_flash('Password must be at least 8 characters.', 'danger');
        header('Location: ' . BASE_URL . 'account/change-password');
        exit();
    }
    if ($new != $confirm) {
        set_flash('Passwords do not match.', 'danger');
        header('Location: ' . BASE_URL . 'account/change-password');
        exit();
    }

    $userModel = new User($pdo);
    $userModel->setPassword(current_user()['id'], $new);

    // Refresh session so must_change_password is no longer set
    $_SESSION['user_id'] = current_user()['id'];

    set_flash('Password updated successfully.', 'success');
    $role = current_user()['role'];
    header('Location: ' . BASE_URL . ($role === 'admin' ? 'admin/users' :
        ($role === 'seller' ? 'seller/dashboard' : 'browse')));
    exit();
}
?>