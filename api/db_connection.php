<?php
$host = $_ENV["PG_HOST"] ?? "localhost";
$port = $_ENV["PG_PORT"] ?? "5432";
$db   = $_ENV["PG_DB"]   ?? "mangasaydb";
$user = $_ENV["PG_USER"] ?? "postgres";
$pass = $_ENV["PG_PASSWORD"] ?? "09953835794Sf";

// ✅ Fix: define $password for PDO use
$password = $pass;

// pg_connect
$conn = pg_connect("host=$host port=$port dbname=$db user=$user password=$pass");

if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

// PDO setup
$dsn = "pgsql:host=$host;port=$port;dbname=$db";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $password, $options);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
