<?php
/**
 * Settings Page
 *
 * A simple placeholder page for client settings.  This page demonstrates
 * where user preferences or account-related configurations could be managed.
 * At present, it displays a friendly message indicating that the feature
 * is under construction.  Future enhancements might include options to
 * configure notification preferences, manage two-factor authentication,
 * or update additional contact details.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

// Redirect unauthenticated users
if (!isset($_SESSION['client_id'])) {
    header('Location: ../login.php');
    exit;
}

// Include the layout components
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/sidebar.php';
require_once __DIR__ . '/../partials/navbar.php';
?>

<div class="content">
    <h4 class="mb-4">Settings</h4>
    <div class="card shadow-sm">
        <div class="card-body">
            <p>This section is under construction.  Check back soon for more options to personalize your experience.</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>