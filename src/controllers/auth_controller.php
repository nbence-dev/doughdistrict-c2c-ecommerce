<?php
// Handles register, login, profile edit, and both password-change flows. Each
// block keys off a hidden field name in the submitted form, so one controller
// can serve several forms.

// Register: all new accounts start as 'buyer'. A buyer can later upgrade to
// seller through onboarding; admins are never created here.
if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['register'])) {

    $name             = trim($_POST['name']) ?? '';
    $email            = trim($_POST['email']) ?? '';
    $password         = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone_number     = trim($_POST['phone_number'] ?? '');

    $error = null;

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password) || empty($phone_number)) {
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
    $created = $userModel->create($name, $email, $password, 'buyer', $phone_number);
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

    // password_verify checks the submitted password against the stored hash.
    if ($user && password_verify($password, $user['password'])) {
        // New session id on login to prevent session-fixation attacks.
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];

        // Invited admins (and anyone flagged) must set a real password first.
        if ($user['must_change_password']) {
            set_flash('Please set a new password to continue.', 'warning');
            header('Location: ' . BASE_URL . 'account/change-password');
            exit();
        }

        // Send each role to its own home screen.
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_flash('Please enter a valid name and email address.', 'danger');
        header('Location: ' . BASE_URL . 'account/profile');
        exit();
    }

    $userModel = new User($pdo);
    $updated   = $userModel->updateProfile((int) current_user()['id'], $name, $email);

    if (!$updated) {
        set_flash('That email address is already in use by another account.', 'danger');
        header('Location: ' . BASE_URL . 'account/profile');
        exit();
    }

    set_flash('Profile updated successfully.', 'success');
    header('Location: ' . BASE_URL . 'account/profile');
    exit();
}

// Voluntary password change from the profile page: requires the current
// password before allowing a new one (unlike the forced flow below).
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $userModel = new User($pdo);
    $user      = $userModel->find((int) current_user()['id']);

    if (!password_verify($current, $user['password'])) {
        set_flash('Current password is incorrect.', 'danger');
        header('Location: ' . BASE_URL . 'account/profile');
        exit();
    }
    if (strlen($new) < 8) {
        set_flash('New password must be at least 8 characters.', 'danger');
        header('Location: ' . BASE_URL . 'account/profile');
        exit();
    }
    if ($new !== $confirm) {
        set_flash('New passwords do not match.', 'danger');
        header('Location: ' . BASE_URL . 'account/profile');
        exit();
    }

    $userModel->setPassword((int) current_user()['id'], $new);
    set_flash('Password changed successfully.', 'success');
    header('Location: ' . BASE_URL . 'account/profile');
    exit();
}

// Forced password change (the must_change_password flow). No current password
// is asked for because the user only has the temp one from their invite. After
// setPassword clears the flag, we send them on to their role's home page.
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