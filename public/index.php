<?php
// Database credentials
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

// Added a deployment timestamp (ZA Time)
date_default_timezone_set('Africa/Johannesburg');
$deployTime = date("H:i:s");
?>

<!DOCTYPE html>
<html>
<head>
    <title>DoughDistrict Pulse Check</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white flex items-center justify-center h-screen">
    <div class="p-8 bg-gray-800 rounded-xl shadow-2xl border-t-4 border-orange-500 text-center w-full max-w-md">
        <h1 class="text-3xl font-bold mb-2">🍩 DoughDistrict</h1>
        <p class="text-gray-400 text-sm mb-6 italic">C2C Marketplace • Cape Town, ZA</p>
        
        <div class="inline-block px-4 py-2 rounded-full font-mono text-sm mb-6" style="background-color: <?php echo $color; ?>22; color: <?php echo $color; ?>;">
            <?php echo $status; ?>
        </div>

        <div class="mb-6">
            <span class="bg-blue-900/30 text-blue-400 text-[10px] uppercase tracking-widest px-2 py-1 rounded border border-blue-500/30">
                CI/CD Sync: Active at <?php echo $deployTime; ?>
            </span>
        </div>

        <?php if(isset($error)): ?>
            <div class="bg-red-900/50 p-4 rounded text-red-200 text-xs text-left">
                <strong>Error:</strong> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="mt-8 pt-6 border-t border-gray-700 text-gray-500 text-xs space-y-1">
            <p>PHP: <?php echo phpversion(); ?></p>
            <p>Environment: Docker Container</p>
        </div>
    </div>
</body>
</html>