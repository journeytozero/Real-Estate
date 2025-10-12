<?php
session_start();
require_once __DIR__ . "/../config/db.php";

if (!isset($_SESSION['agent_id']) || !isset($_GET['rented_id'])) {
    die("Unauthorized access.");
}

$rented_id = (int)$_GET['rented_id'];

// Fetch transaction details
$stmt = $conn->prepare("
    SELECT r.id AS rented_id, p.name AS property_name, p.location, p.price, r.category, 
           c.name AS client_name, c.email AS client_email, r.rented_start_date, r.rented_end_date, r.created_at
    FROM rented r
    JOIN properties p ON r.property_id = p.id
    JOIN clients c ON r.client_id = c.id
    WHERE r.id = ?
");
$stmt->execute([$rented_id]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transaction) die("Transaction not found.");

// Fetch property photos
$photosStmt = $conn->prepare("SELECT photo_path FROM rented_photos WHERE rented_id = ?");
$photosStmt->execute([$rented_id]);
$photos = $photosStmt->fetchAll(PDO::FETCH_ASSOC);

// Company info
$company_name = "RealEstate Agency";
$company_logo = "../assets/images/logo.png"; // Replace with your logo path
$company_address = "123 Main Street, City, Country";
$company_email = "info@realestate.com";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - <?= htmlspecialchars($transaction['property_name']) ?></title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <style>
        body { padding: 20px; font-family: Arial, sans-serif; }
        .invoice-box { border: 1px solid #eee; padding: 30px; max-width: 900px; margin: auto; }
        .invoice-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .invoice-header img { max-width: 120px; }
        .invoice-table th, .invoice-table td { padding: 10px; border: 1px solid #ddd; }
        .photos img { max-width: 150px; margin: 5px; border: 1px solid #ddd; padding: 3px; }
        @media print { 
            .btn-print { display: none; } 
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <!-- Header -->
        <div class="invoice-header">
            <div>
                <h2><?= htmlspecialchars($company_name) ?></h2>
                <p><?= htmlspecialchars($company_address) ?><br>Email: <?= htmlspecialchars($company_email) ?></p>
            </div>
            <div>
                <img src="<?= $company_logo ?>" alt="Company Logo">
            </div>
        </div>

        <!-- Invoice Info -->
        <div class="mb-4">
            <h4>Invoice</h4>
            <p><strong>Invoice Date:</strong> <?= date("M d, Y", strtotime($transaction['created_at'])) ?><br>
            <strong>Invoice #: </strong> INV-<?= str_pad($transaction['rented_id'], 5, "0", STR_PAD_LEFT) ?></p>
        </div>

        <!-- Client Info -->
        <div class="mb-4">
            <h5>Client Information</h5>
            <p>
                <strong>Name:</strong> <?= htmlspecialchars($transaction['client_name']) ?><br>
                <strong>Email:</strong> <?= htmlspecialchars($transaction['client_email']) ?>
            </p>
        </div>

        <!-- Property Info -->
        <div class="mb-4">
            <h5>Property Details</h5>
            <table class="table table-bordered invoice-table">
                <thead>
                    <tr>
                        <th>Property</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>Rent Start</th>
                        <th>Rent End</th>
                        <th>Price ($)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= htmlspecialchars($transaction['property_name']) ?></td>
                        <td><?= htmlspecialchars($transaction['category']) ?></td>
                        <td><?= htmlspecialchars($transaction['location']) ?></td>
                        <td><?= htmlspecialchars($transaction['rented_start_date']) ?></td>
                        <td><?= $transaction['rented_end_date'] ? htmlspecialchars($transaction['rented_end_date']) : 'Ongoing' ?></td>
                        <td><?= number_format($transaction['price'], 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Property Photos -->
        <?php if ($photos): ?>
            <div class="mb-4">
                <h5>Property Photos</h5>
                <div class="photos d-flex flex-wrap">
                    <?php foreach ($photos as $photo): ?>
                        <img src="../<?= htmlspecialchars($photo['photo_path']) ?>" alt="Property Photo">
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Footer -->
        <div class="text-end">
            <p>Thank you for your business!</p>
            <button onclick="window.print()" class="btn btn-primary btn-print">Print Invoice</button>
        </div>
    </div>
</body>
</html>
