<?php
session_start();
require_once __DIR__ . "/config/db.php"; // adjust path to your db connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Please fill in all fields.";
        header("Location: index.php"); // back to home
        exit;
    }

    try {
        // Check user
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // ✅ Store session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            // ✅ Redirect by role
            if ($user['role'] === 'admin') {
                header("Location: admin/dashboard.php");
            } elseif ($user['role'] === 'agent') {
                header("Location: agent_dashboard.php");
            } elseif ($user['role'] === 'client') {
                header("Location: client_dashboard.php");
            } else {
                $_SESSION['error'] = "Invalid account role.";
                header("Location: hero.php");
            }
            exit;
        } else {
            $_SESSION['error'] = "Invalid email or password.";
            header("Location: hero.php");
            exit;
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: hero.php");
        exit;
    }
}
?>
