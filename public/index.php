<?php
/**
 * DoughDistrict Database Connection
 * Optimized for GitHub Actions + Docker Environment
 */

// 1. Pull variables injected by Docker (from your .env/GitHub Secrets)
$host = getenv('DB_HOST') ?: 'dough-db';
$db   = getenv('DB_NAME') ?: 'doughdistrict';
$user = getenv('DB_USER') ?: 'doughuser';
$pass = getenv('DB_PASS') ?: 'doughpass';
$port = getenv('DB_PORT') ?: '3306';

$dbStatus = "❌ Database Disconnected";
$statusColor = "text-red-400";
$errorMsg = "";

try {
    // 2. Set up the DSN (Data Source Name)
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    
    // 3. Connection Options for Security & Performance
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    // 4. Attempt the connection
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    $dbStatus = "✅ Database Connected";
    $statusColor = "text-green-400";

} catch (\PDOException $e) {
    // If it fails, capture the message for debugging
    $errorMsg = "Error: " . $e->getMessage();
}

// Get the current time for the "Sync Badge"
date_default_timezone_set('Africa/Johannesburg');
$syncTime = date('H:i:s');
?>