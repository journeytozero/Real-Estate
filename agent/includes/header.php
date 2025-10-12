<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Dashboard - DreamHomes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            overflow-x: hidden;
        }

        .sidebar {
            min-height: 100vh;
            background: #0d6efd;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 60px;
        }

        .sidebar .nav-link {
            color: #fff;
        }

        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            border-radius: .5rem;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
        }

        .navbar {
            margin-left: 250px;
        }
    </style>
</head>

<body>