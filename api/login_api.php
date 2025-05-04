<?php
session_start();
include('db_connection.php'); // this should return $pdo (PDO connection)

// 1. Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the data from the form
    $email = $_POST['email'];
    $pass = $_POST['password'];

    // Hash the input password using SHA-256
    $hashedPassword = hash('sha256', $pass);

    // 2. Prepare and execute the query using PDO
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 3. If user exists, verify password
    if ($user && $hashedPassword === $user['password']) {
        $_SESSION['email'] = $user['email'];
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];

        header("Location: HomeLogin.php");
        exit();
    } else {
        header("Location: login.php?error=Invalid email or password");
        exit();
    }
}
?>
