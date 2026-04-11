<?php 
    // Read database connection parameters from environment variables
    // (injected by docker-compose — no need to parse a file)
    $dbHost = getenv('DB_HOST') ?: 'dough-db';
    $dbPort = getenv('DB_PORT') ?: '3306';
    $dbName = getenv('DB_NAME');
    $dbUser = getenv('DB_USER');
    $dbPass = getenv('DB_PASS');

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