<?php
// Database credentials from your docker-compose environment variables
$host = getenv('DB_HOST');
$db   = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');

$status = "❌ Database Disconnected";
$color = "#ff4757";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $status = "✅ Database Connected: " . $db;
    $color = "#2ed573";
} catch (PDOException $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>DoughDistrict Pulse Check</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white flex items-center justify-center h-screen">
    <div class="p-8 bg-gray-800 rounded-xl shadow-2xl border-t-4 border-orange-500 text-center">
        <h1 class="text-3xl font-bold mb-4">🍩 DoughDistrict Status</h1>
        <div class="inline-block px-4 py-2 rounded-full font-mono text-sm mb-6" style="background-color: <?php echo $color; ?>22; color: <?php echo $color; ?>;">
            <?php echo $status; ?>
        </div>
        
        <?php if(isset($error)): ?>
            <div class="bg-red-900/50 p-4 rounded text-red-200 text-xs text-left">
                <strong>Error:</strong> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="mt-6 text-gray-400 text-sm">
            <p>PHP Version: <?php echo phpversion(); ?></p>
            <p>Server: <?php echo $_SERVER['SERVER_SOFTWARE']; ?></p>
        </div>
    </div>
</body>
</html>
