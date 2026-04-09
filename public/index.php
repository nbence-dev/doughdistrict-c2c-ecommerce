<?php
/**
 * Core Connection Logic
 */
$host = getenv('DB_HOST') ?: 'dough-db';
$db   = getenv('DB_NAME') ?: 'doughdistrict';
$user = getenv('DB_USER') ?: 'doughuser';
$pass = getenv('DB_PASS') ?: 'doughpass';
$port = getenv('DB_PORT') ?: '3306';

$dbStatus = "❌ Database Disconnected";
$statusColor = "text-red-400";
$errorMsg = "";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);
    $dbStatus = "✅ Database Connected";
    $statusColor = "text-green-400";

} catch (\PDOException $e) {
    $errorMsg = $e->getMessage();
}

date_default_timezone_set('Africa/Johannesburg');
$syncTime = date('H:i:s');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Portal | Development</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #0a0a0a; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="text-gray-200 min-h-screen flex items-center justify-center p-6">

    <div class="glass border border-gray-800 w-full max-w-md rounded-2xl p-8 shadow-2xl">
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-white">Project Alpha</h1>
                <p class="text-gray-500 text-sm">C2C Marketplace Platform</p>
            </div>
            <div class="bg-blue-500/10 text-blue-400 text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider border border-blue-500/20">
                Dev Mode
            </div>
        </div>

        <div class="bg-black/40 border border-gray-800 rounded-xl p-5 mb-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-gray-500 uppercase font-semibold">Live Pipeline Status</span>
                <span class="flex h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
            </div>
            
            <div class="flex items-baseline space-x-2">
                <span class="text-lg font-medium <?php echo $statusColor; ?>">
                    <?php echo $dbStatus; ?>
                </span>
            </div>
            
            <p class="text-[10px] text-gray-600 mt-2 font-mono">
                Last CI/CD Sync: <?php echo $syncTime; ?> (SAST)
            </p>

            <?php if ($errorMsg): ?>
            <div class="mt-4 p-3 bg-red-900/10 border border-red-500/30 rounded-lg">
                <p class="text-[10px] text-red-400 font-mono break-all line-clamp-2">
                    <?php echo htmlspecialchars($errorMsg); ?>
                </p>
            </div>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-2 gap-3 mb-8">
            <div class="p-3 border border-gray-800/50 rounded-lg">
                <p class="text-[9px] text-gray-500 uppercase font-bold mb-1">Runtime</p>
                <p class="text-xs font-mono text-gray-300">PHP 8.2 • Docker</p>
            </div>
            <div class="p-3 border border-gray-800/50 rounded-lg">
                <p class="text-[9px] text-gray-500 uppercase font-bold mb-1">Region</p>
                <p class="text-xs font-mono text-gray-300">CPT-ZA-01</p>
            </div>
        </div>

        <footer class="text-center">
            <p class="text-[10px] text-gray-600 uppercase tracking-[0.2em]">
                System Authenticated & Secured
            </p>
        </footer>
    </div>

</body>
</html>