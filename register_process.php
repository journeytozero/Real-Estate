<?php
session_start();
require_once __DIR__ . "/config/db.php"; // DB connection

$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm_password']);
    $role     = trim($_POST['role']);
    $trade_license = $_POST['trade_license'] ?? null;

    // Save form values to repopulate fields on error
    $_SESSION['form_data'] = [
        'name' => $name,
        'email' => $email,
        'role' => $role,
        'trade_license' => $trade_license
    ];

    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm) || empty($role)) {
        $_SESSION['register_msg'] = "All fields are required.";
        $_SESSION['register_modal'] = true;
        header("Location: idx.php");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['register_msg'] = "Invalid email format.";
        $_SESSION['register_modal'] = true;
        header("Location: idx.php");
        exit;
    }

    if ($password !== $confirm) {
        $_SESSION['register_msg'] = "Passwords do not match.";
        $_SESSION['register_modal'] = true;
        header("Location: idx.php");
        exit;
    }

    try {
        // Upload document if seller
        $document = null;
        if ($role === "seller" && isset($_FILES['document']) && $_FILES['document']['error'] === 0) {
            $allowed = ["pdf","jpg","jpeg"];
            $ext = strtolower(pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                $_SESSION['register_msg'] = "Invalid file type. Only PDF/JPG allowed.";
                $_SESSION['register_modal'] = true;
                header("Location: idx.php");
                exit;
            }

            if ($_FILES['document']['size'] > 5*1024*1024) {
                $_SESSION['register_msg'] = "File too large. Max 5MB.";
                $_SESSION['register_modal'] = true;
                header("Location: idx.php");
                exit;
            }

            $document = "uploads/docs/" . uniqid() . "." . $ext;
            move_uploaded_file($_FILES['document']['tmp_name'], $document);
        }

        // Insert directly into clients or agents
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        if ($role === "buyer") {
            $stmt = $conn->prepare("INSERT INTO clients (name, email, password, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$name, $email, $hashed]);
        } elseif ($role === "seller") {
            $stmt = $conn->prepare("INSERT INTO agents (name, email, password, trade_license, document, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$name, $email, $hashed, $trade_license, $document]);
        }

        // Clear saved form data
        unset($_SESSION['form_data']);

        $_SESSION['register_success'] = "Registration successful! Redirecting to login...";
        $_SESSION['register_modal'] = true;
        header("Location: idx.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['register_msg'] = "Database error: " . $e->getMessage();
        $_SESSION['register_modal'] = true;
        header("Location: idx.php");
        exit;
    }
}
?>
