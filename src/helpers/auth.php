<?php
    // Session management
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Load Constants
    require_once __DIR__ . '/../config/constants.php';
    require_once __DIR__ . '/../models/User.php';

    // Load Database (which uses constants)
    $pdo = require_once ROOT_PATH . '/config/db.php';

    // Returns the logged-in user row, or null if nobody is logged in.
    // The static $fetched/$cached pair means the DB is only hit once per request
    // no matter how many times this is called during a page load.
    function current_user() {
        static $fetched = false;
        static $cached  = null;
        if ($fetched) return $cached;
        $fetched = true;

        global $pdo;

        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        $userModel = new User($pdo);
        $result    = $userModel->find($_SESSION['user_id']);
        $cached    = $result ?: null;
        return $cached;
    }

    function is_logged_in() {
        return isset($_SESSION['user_id']);
    }

    // Gate for any page that needs a logged-in user. Bounces to /login if not
    // authenticated, and also if the account was deactivated mid-session (in
    // which case we destroy the stale session). Finally, if the user still has
    // a temp password (must_change_password), force them onto the change-password
    // page and let nowhere else through until it's done.
    function require_login() {
        if (!is_logged_in()) {
            header('Location: ' . BASE_URL . 'login');
            exit();
        }
        $user = current_user();
        if (!$user || !($user['is_active'] ?? 1)) {
            session_destroy();
            header('Location: ' . BASE_URL . 'login');
            exit();
        }
        $current_path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        if ($user && ($user['must_change_password'] ?? 0) && $current_path !== 'account/change-password') {
            header('Location: ' . BASE_URL . 'account/change-password');
            exit();
        }
    }

    // Stricter gate: logged in AND holding a specific role. Used to keep buyers
    // out of seller/admin areas. Runs require_login() first so all of its checks
    // (active account, forced password change) apply here too.
    function require_role($role) {
        require_login();
        $user = current_user();
        if (!$user || $user['role'] !== $role) {
            header('Location: ' . BASE_URL . 'login');
            exit();
        }
    }


?>