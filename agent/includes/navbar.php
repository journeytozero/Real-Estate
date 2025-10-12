<?php
ob_start();
$agent_name = $_SESSION['agent_name'] ?? 'Agent';
?>

<nav class="navbar navbar-expand navbar-light bg-light shadow-sm sticky-top">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold text-primary">DreamHomes</span>
        <div class="ms-auto">
            <span class="me-3"><i class="fas fa-user-circle"></i> Welcome, <?= htmlspecialchars($agent_name) ?></span>
            <a href="../logout.php" class="btn btn-sm btn-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</nav>
