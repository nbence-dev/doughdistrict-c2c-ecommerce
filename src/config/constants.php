<?php
    //Go up level to define root
    define('ROOT_PATH', dirname(__DIR__));

    // URL path
    define('BASE_URL', '/');

    // Full origin URL — derived from the current request so it works on any domain
    (function () {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        define('BASE_FULL_URL', $scheme . '://' . $host);
    })();

    // Asset paths
    define('ASSETS_URL', BASE_URL . 'assets/');
    define('CSS_URL', ASSETS_URL . 'css/');
    define('JS_URL', ASSETS_URL . 'js/');
    define('IMAGES_URL', ASSETS_URL . 'images/');

    // App Info
    define('APP_NAME','DoughDistrict');
?>