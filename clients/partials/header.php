<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$page_title = $page_title ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($page_title) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="/RS/assets/css/style.css" />
  <style>
    /* Sidebar styles */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    width: 250px;
    background-color: #212529;
    transition: width 0.3s ease;
    overflow-x: hidden;
    z-index: 1040;
}

.sidebar .nav-link {
    font-weight: 500;
    padding: 12px 15px;
    border-radius: 6px;
}

.sidebar .nav-link:hover,
.sidebar .nav-link.active {
    background-color: #0d6efd;
    color: #fff !important;
}

.sidebar-collapsed {
    width: 0 !important;
}

/* Content */
.content {
    margin-left: 250px;
    padding: 20px;
    transition: margin-left 0.3s ease;
}

/* Shift content when sidebar collapsed */
.sidebar-collapsed ~ .content {
    margin-left: 0;
}

/* Responsive: hide sidebar by default on small screens */
@media (max-width: 991.98px) {
    .sidebar {
        width: 0;
    }
    .sidebar.sidebar-collapsed {
        width: 250px !important;
    }
    .content {
        margin-left: 0;
    }
    .sidebar.sidebar-collapsed ~ .content {
        margin-left: 250px;
    }
}

  </style>
</head>
<body>
