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

    function require_role($role) {
        require_login();
        $user = current_user();
        if (!$user || $user['role'] !== $role) {
            header('Location: ' . BASE_URL . 'login');
            exit();
        }
    }


?>