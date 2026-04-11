<?php
// Bootstrap the application
require_once __DIR__ . '/../src/config/constants.php';
require_once ROOT_PATH . '/helpers/auth.php';   // also loads db.php and User.php
require_once ROOT_PATH . '/helpers/flash.php';

// Parse the request path
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Router
switch ($path) {

    case '':
        // Root: send logged-in users to browse, guests to login
        if (is_logged_in()) {
            header('Location: ' . BASE_URL . 'browse');
        } else {
            header('Location: ' . BASE_URL . 'login');
        }
        exit();

    case 'login':
        require_once ROOT_PATH . '/controllers/auth_controller.php';
        require_once ROOT_PATH . '/views/auth/login.php';
        break;

    case 'register':
        require_once ROOT_PATH . '/controllers/auth_controller.php';
        require_once ROOT_PATH . '/views/auth/register.php';
        break;

    case 'logout':
        set_flash('You have been logged out.', 'success');
        session_unset();
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/');
        header('Location: ' . BASE_URL . 'login');
        exit();
    
    case 'browse':
        require_login();
        require_once ROOT_PATH . '/controllers/browse_controller.php';
        require_once ROOT_PATH . '/views/buyer/browse.php'; 
        break;

    case 'admin/dashboard':
        require_role('admin');
        require_once ROOT_PATH . '/controllers/admin_controller.php';
        require_once ROOT_PATH . '/views/admin/dashboard.php';
        break;
    
    case 'seller/dashboard':
        require_role('seller');
        require_once ROOT_PATH . '/controllers/seller_controller.php';
        require_once ROOT_PATH . '/views/seller/dashboard.php';
        break;
        
    default:
        http_response_code(404);
        echo '<h1>404 — Page not found</h1>';
        break;
}
