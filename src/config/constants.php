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

    /**
     * Build an asset URL with a cache-busting version query based on the file's
     * last-modified time. The ?v= value only changes when the file changes, so
     * browsers re-download updated CSS/JS automatically but keep caching unchanged
     * files. $path is relative to public/assets/ (e.g. 'js/checkout.js').
     */
    function asset(string $path): string {
        $file = dirname(__DIR__, 2) . '/public/assets/' . $path;
        $version = is_file($file) ? filemtime($file) : '';
        return ASSETS_URL . $path . ($version ? '?v=' . $version : '');
    }
?>