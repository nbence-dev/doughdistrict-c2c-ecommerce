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
        // Tells function to look for $pdo variable created outside this function in your page logic
        global $pdo;

        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        $userModel = new User($pdo);
        return $userModel->find($_SESSION['user_id']);
    }

    function is_logged_in() {
        return isset($_SESSION['user_id']);
    }

    function require_login() {
        if (!is_logged_in()) {
            header('Location: ' . BASE_URL . 'login');
            exit();
        }
    }

    function require_role($role) {
        $user = current_user();
        if (!$user || $user['role'] !== $role) {
            header('Location: ' . BASE_URL . 'login');
            exit();
        }
    }


?>