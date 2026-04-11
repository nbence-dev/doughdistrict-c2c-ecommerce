<?php 
    // Building path to .env file
    $envPath = __DIR__ . '/../.env';

    // Parse the file into associative array
    $env = parse_ini_file($envPath);

    // Database connection parameters
    $dbHost = $env['DB_HOST'];
    $dbPort = $env['DB_PORT'];
    $dbName = $env['DB_NAME'];
    $dbUser = $env['DB_USER'];
    $dbPass = $env['DB_PASS'];

    // Create DSN (Data Source Name) for PDO

    $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4";

    // Set PDO options
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Fetch associative arrays
        PDO::ATTR_EMULATE_PREPARES => false, // Use native prepared statements
    ];

    // try catch block to handle connection errors
    try {
        $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
        return $pdo; // Return the PDO instance for use in other parts of the application
    } catch (PDOException $e) {
        // Handle connection error
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
        
    }
?>