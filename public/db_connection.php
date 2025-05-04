<?php
phpinfo();
$host = $_ENV['PG_HOST'];  // Neon PostgreSQL host
$port = $_ENV['PG_PORT'];  // Neon PostgreSQL port (usually 5432)
$db = $_ENV['PG_DB'];  // Neon PostgreSQL database name
$user = $_ENV['PG_USER'];  // Neon PostgreSQL username
$password = $_ENV['PG_PASSWORD'];  // Neon PostgreSQL password

// Create a DSN (Data Source Name) string for PDO
$dsn = "pgsql:host=$host;port=$port;dbname=$db";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  // Fetch results as associative arrays
    PDO::ATTR_EMULATE_PREPARES => false,  // Disable emulated prepared statements
];

try {
    // Create a PDO instance and connect to the database
    $pdo = new PDO($dsn, $user, $password, $options);
    echo "Connected successfully to Neon PostgreSQL!";
} catch (PDOException $e) {
    // Catch any exceptions and display the error message
    echo "Connection failed: " . $e->getMessage();
}
?>
