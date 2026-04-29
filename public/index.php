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

    case 'product':
        require_login();
        require_once ROOT_PATH . '/controllers/browse_controller.php';
        require_once ROOT_PATH . '/views/buyer/product_detail.php';
        break;

    case 'cart':
        require_login();
        require_once ROOT_PATH . '/controllers/cart_controller.php';
        require_once ROOT_PATH . '/views/buyer/cart.php';
        break;

    case 'cart/add':
        require_login();
        require_once ROOT_PATH . '/controllers/cart_controller.php';
        break;

    case 'cart/remove':
        require_login();
        require_once ROOT_PATH . '/controllers/cart_controller.php';
        break;


    case 'cart/update':
        require_login();
        require_once ROOT_PATH . '/controllers/cart_controller.php';
        break;


    // case 'admin/dashboard':
    //     require_role('admin');
    //     require_once ROOT_PATH . '/controllers/admin_controller.php';
    //     require_once ROOT_PATH . '/views/admin/dashboard.php';
    //     break;

    case 'admin/users':
        require_role('admin');
        require_once ROOT_PATH . '/controllers/admin_controller.php';
        require_once ROOT_PATH . '/views/admin/users.php';
        break;

    case 'admin/users/toggle':
        require_role('admin');
        require_once ROOT_PATH . '/controllers/admin_controller.php';
        break;

    case 'admin/users/role':
        require_role('admin');
        require_once ROOT_PATH . '/controllers/admin_controller.php';
        break;

    case 'admin/products':
        require_role('admin');
        require_once ROOT_PATH . '/controllers/admin_controller.php';
        require_once ROOT_PATH . '/views/admin/products.php';
        break;

    // case 'admin/products/toggle':
    //     require_role('admin');
    //     require_once ROOT_PATH . '/controllers/admin_controller.php';
    //     break;

    case 'admin/products/status':
        require_role('admin');
        require_once ROOT_PATH . '/controllers/admin_controller.php';
        break;

    case 'admin/categories':
        require_role('admin');
        require_once ROOT_PATH . '/controllers/admin_controller.php';
        require_once ROOT_PATH . '/views/admin/categories.php';
        break;

    case 'admin/categories/create':
        require_role('admin');
        require_once ROOT_PATH . '/controllers/admin_controller.php';
        break;

    case 'admin/categories/update':
        require_role('admin');
        require_once ROOT_PATH . '/controllers/admin_controller.php';
        break;

    case 'admin/categories/delete':
        require_role('admin');
        require_once ROOT_PATH . '/controllers/admin_controller.php';
        break;

    // ── Onboarding (buyer only — becomes seller on submit) ──────────────
    case 'seller/onboard':
        require_login();
        require_once ROOT_PATH . '/controllers/seller_controller.php';
        require_once ROOT_PATH . '/views/seller/onboarding.php';
        break;

    // ── Seller dashboard ─────────────────────────────────────────────
    case 'seller/dashboard':
        require_role('seller');
        require_once ROOT_PATH . '/controllers/seller_controller.php';
        require_once ROOT_PATH . '/views/seller/dashboard.php';
        break;

    // ── Shop profile ─────────────────────────────────────────────────
    case 'seller/profile':
        require_role('seller');
        require_once ROOT_PATH . '/controllers/seller_controller.php';
        require_once ROOT_PATH . '/views/seller/profile.php';
        break;

    // ── Products ─────────────────────────────────────────────────────
    case 'seller/products':
        require_role('seller');
        require_once ROOT_PATH . '/controllers/seller_controller.php';
        require_once ROOT_PATH . '/views/seller/products/index.php';
        break;

    case 'seller/products/create':
        require_role('seller');
        require_once ROOT_PATH . '/controllers/seller_controller.php';
        require_once ROOT_PATH . '/views/seller/products/create.php';
        break;

    case 'seller/products/edit':
        require_role('seller');
        require_once ROOT_PATH . '/controllers/seller_controller.php';
        require_once ROOT_PATH . '/views/seller/products/edit.php';
        break;

    case 'seller/products/delete':
        require_role('seller');
        require_once ROOT_PATH . '/controllers/seller_controller.php';
        break;

    // ── Stripe Connect ────────────────────────────────────────────────
    case 'seller/stripe/connect':
        require_role('seller');
        require_once ROOT_PATH . '/controllers/seller_controller.php';
        break;

    case 'seller/stripe/callback':
        require_role('seller');
        require_once ROOT_PATH . '/controllers/seller_controller.php';
        break;

    case 'checkout':
        require_login();
        require_once ROOT_PATH . '/controllers/checkout_controller.php';
        require_once ROOT_PATH . '/views/buyer/checkout.php';
        break;

    case 'checkout/confirm':
        require_login();
        require_once ROOT_PATH . '/controllers/checkout_controller.php';
        break;

    case 'order/confirmation':
        require_login();
        require_once ROOT_PATH . '/controllers/checkout_controller.php';
        require_once ROOT_PATH . '/views/buyer/order_confirmation.php';
        break;

    // ── Orders (Phase 6) ─────────────────────────────────────────────
    // TODO: implement in Phase 6
    case 'seller/orders':
        require_role('seller');
        // require_once ROOT_PATH . '/controllers/seller_controller.php';
        // require_once ROOT_PATH . '/views/seller/orders/index.php';
        break;

    case 'seller/orders/detail':
        require_role('seller');
        // require_once ROOT_PATH . '/controllers/seller_controller.php';
        // require_once ROOT_PATH . '/views/seller/orders/detail.php';
        break;

    case 'seller/orders/ship':
        require_role('seller');
        // require_once ROOT_PATH . '/controllers/seller_controller.php';
        break;

    default:
        http_response_code(404);
        require_once ROOT_PATH . '/views/errors/404.php';
        break;
}
