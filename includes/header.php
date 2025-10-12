<?php
// Start session safely (only once)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Optional: Include database and Auth class if needed globally
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../Auth.php";

// Create Auth instance if needed
$auth = new Auth($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DreamHomes</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Roboto:wght@300;400&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">

    <style>
        /* Navbar Customizations */
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }

        .navbar-nav .nav-link {
            font-weight: 500;
            margin: 0 10px;
            transition: color 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: #007bff !important;
        }

        .btn-custom {
            background-color: #007bff;
            border-color: #007bff;
            font-weight: 600;
            padding: 8px 20px;
        }

        .btn-custom:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .contact-info {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .hero video {
            object-fit: cover;
            height: 100vh;
        }

        .hero .overlay {
            background: rgba(0, 0, 0, 0.5);
        }

        /* Royal Color Theme */
        :root {
            --royal-purple-dark: #2c003e;
            --royal-purple-light: #4a148c;
            --accent-gold: #D4AF37;
            --text-light: #f8f9fa;
            --text-muted-light: #adb5bd;
        }

        /* Footer Royal Theme */
        .footer-royal {
            background: linear-gradient(135deg, var(--royal-purple-dark), var(--royal-purple-light));
            color: var(--text-light);
            padding-top: 80px;
        }

        .footer-royal .logo-text,
        .footer-royal h5 {
            color: var(--accent-gold);
            font-weight: 600;
        }

        .footer-royal .logo-text {
            font-size: 2rem;
            font-weight: 700;
        }

        .footer-royal p,
        .footer-royal li,
        .footer-royal a {
            color: var(--text-muted-light);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer-royal ul li a:hover {
            color: var(--accent-gold);
            padding-left: 5px;
        }

        .footer-royal .contact-item .icon {
            color: var(--accent-gold);
            font-size: 1.1rem;
            width: 25px;
        }

        .footer-royal .social-icons a {
            background-color: rgba(0, 0, 0, 0.2);
            color: var(--text-light);
            transition: all 0.3s ease;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            border-radius: 50%;
            font-size: 1rem;
        }

        .footer-royal .social-icons a:hover {
            background-color: var(--accent-gold);
            color: var(--royal-purple-dark);
            transform: translateY(-3px);
        }

        .footer-royal .btn-gold {
            background-color: var(--accent-gold);
            border-color: var(--accent-gold);
            color: var(--royal-purple-dark);
            font-weight: 600;
        }

        .footer-royal .btn-gold:hover {
            background-color: #c8a02a;
            border-color: #c8a02a;
        }

        .footer-royal .newsletter-form .form-control {
            background-color: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }

        .footer-royal .newsletter-form .form-control:focus {
            background-color: transparent;
            border-color: var(--accent-gold);
            box-shadow: none;
        }

        .footer-royal .footer-bottom {
            background-color: rgba(0, 0, 0, 0.3);
            border-top: 1px solid rgba(212, 175, 55, 0.3);
            padding: 20px 0;
            margin-top: 60px;
        }
    </style>
</head>

<body>
