<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Real Estate Admin Dashboard</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
/* Sidebar icons */
.nav-link i {
    width: 1.5rem;
    text-align: center;
}

/* Prevent text wrapping in links */
.nav-link {
    white-space: nowrap;
    transition: all 0.2s ease-in-out; /* smooth hover effects */
}

/* Sidebar sticky full-height */
#sidebar {
    position: sticky;       /* sticky while scrolling */
    top: 0;                 /* stick to top */
    height: 100vh;          /* full viewport height */
    width: 250px;           /* sidebar width */
    background-color: #343a40;
    color: #fff;
    overflow-y: auto;       /* scroll if content exceeds height */
}

/* Scrollbar styling for sidebar */
#sidebar::-webkit-scrollbar {
    width: 6px;
}
#sidebar::-webkit-scrollbar-thumb {
    background-color: rgba(255,255,255,0.3);
    border-radius: 3px;
}
#sidebar::-webkit-scrollbar-thumb:hover {
    background-color: rgba(255,255,255,0.6);
}

/* Sidebar links */
#sidebar .nav-link {
    color: #fff;
    padding: 0.5rem 1rem;
    border-radius: 0.25rem;
    margin-bottom: 0.2rem;
}

/* Hover effect for links */
#sidebar .nav-link:hover {
    background-color: #495057;
    color: #fff;
    transform: scale(1.02);
}

/* Active link */
#sidebar .nav-link.active {
    background-color: #007bff;
    color: #fff;
    font-weight: bold;
}

/* Sidebar headings / user info */
#sidebar .sidebar-heading {
    font-size: 1.1rem;
    font-weight: 600;
    text-transform: uppercase;
}

/* Responsive tweaks: smaller padding on mobile */
@media (max-width: 768px) {
    #sidebar {
        width: 200px;
        padding: 0.5rem;
    }
    #sidebar .nav-link {
        font-size: 0.9rem;
        padding: 0.4rem 0.8rem;
    }
}
</style>


</head>
<body>
  <!-- Wrapper Start -->
  <div class="d-flex" id="wrapper">
