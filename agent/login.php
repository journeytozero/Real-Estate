<?php
session_start();
require_once __DIR__ . "/config/db.php";

$error = "";

// Debug mode: shows DB fetch result (set false after testing)
$debug = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        // Fetch agent by email
        $stmt = $conn->prepare("SELECT * FROM agents WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $agent = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($debug) {
            echo "<pre>";
            echo "Email entered: [$email]\n";
            echo "Password entered: [$password]\n";
            echo "DB fetch result:\n";
            print_r($agent);
            echo "</pre>";
            exit;
        }

        // ✅ Check password against hash
        if ($agent && password_verify($password, $agent['password'])) {
            // Login successful
            $_SESSION['agent_id'] = $agent['id'];
            $_SESSION['agent_name'] = $agent['name'];

            header("Location: pages/dashboard.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }

    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agent Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">

<div class="card shadow-lg p-4" style="max-width:400px; width:100%;">
    <h3 class="text-center mb-3">Agent Login</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
</div>

</body>
</html>
