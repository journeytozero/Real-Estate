<?php
require_once __DIR__ . "/admin/partials/db.php"; // PDO connection $pdo

// Start output buffering
ob_start();

// ---------- Handle AJAX Contact Form ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    header('Content-Type: application/json');

    $property_id   = $_POST['property_id'] ?? null;
    $visitor_name  = trim($_POST['name'] ?? '');
    $visitor_email = trim($_POST['email'] ?? '');
    $message       = trim($_POST['message'] ?? '');

    if (!$property_id || !$visitor_name || !$visitor_email || !$message) {
        echo json_encode(['status' => false, 'message' => 'All fields are required.']);
        exit;
    }

    // Fetch agent ID dynamically
    $agentStmt = $pdo->prepare("SELECT agent_id FROM properties WHERE id = ? LIMIT 1");
    $agentStmt->execute([$property_id]);
    $agent = $agentStmt->fetch(PDO::FETCH_ASSOC);
    $agent_id = $agent['agent_id'] ?? null;

    try {
        $insert = $pdo->prepare("
            INSERT INTO messages (property_id, agent_id, clients_name, clients_email, message, created_at)
            VALUES (:property_id, :agent_id, :clients_name, :clients_email, :message, NOW())
        ");
        $insert->execute([
            ':property_id' => $property_id,
            ':agent_id' => $agent_id,
            ':clients_name' => $visitor_name,
            ':clients_email' => $visitor_email,
            ':message' => $message
        ]);

        echo json_encode(['status' => true, 'message' => '✅ Your message has been sent successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => false, 'message' => '⚠️ Failed to send message.']);
    }
    exit;
}

// ---------- Page Rendering ----------
$property_id = $_GET['id'] ?? null;
if (!$property_id) die("Property not specified.");

// Fetch property info with category & agent
$stmt = $conn->prepare("
    SELECT p.*, 
           c.name AS category_name, 
           a.name AS agent_name, 
           a.email AS agent_email, 
           a.phone AS agent_phone
    FROM properties p
    LEFT JOIN property_categories c ON p.category_id = c.id
    LEFT JOIN agents a ON p.agent_id = a.id
    WHERE p.id = ? LIMIT 1
");
$stmt->execute([$property_id]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$property) die("Property not found.");

// Fetch multiple photos
$photosStmt = $conn->prepare("SELECT photo FROM property_photos WHERE property_id = ?");
$photosStmt->execute([$property_id]);
$photos = $photosStmt->fetchAll(PDO::FETCH_ASSOC);

ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($property['name']) ?> - Property Details</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.carousel-item img { max-height: 400px; object-fit: cover; }
</style>
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row">
        <!-- Property Photos -->
        <div class="col-md-7">
            <?php if ($photos): ?>
                <div id="propertyCarousel" class="carousel slide shadow rounded" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php foreach ($photos as $index => $p): ?>
                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                <img src="admin/uploads/<?= htmlspecialchars($p['photo']) ?>" 
                                     class="d-block w-100 rounded" alt="Property Photo">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#propertyCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#propertyCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            <?php else: ?>
                <img src="https://via.placeholder.com/800x400?text=No+Image" 
                     class="img-fluid rounded shadow" alt="No image">
            <?php endif; ?>
        </div>

        <!-- Property Info -->
        <div class="col-md-5">
            <h2><?= htmlspecialchars($property['name']) ?></h2>
            <p class="text-muted"><?= htmlspecialchars($property['category_name'] ?? "N/A") ?></p>
            <h4 class="text-success mb-3">৳ <?= number_format($property['price'], 2) ?></h4>
            <p><strong>Location:</strong> <?= htmlspecialchars($property['location']) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($property['status']) ?></p>
            <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($property['description'])) ?></p>

            <hr>
            <h5>Agent Information</h5>
            <p>
                <?= htmlspecialchars($property['agent_name'] ?? "N/A") ?><br>
                Email: <?= htmlspecialchars($property['agent_email'] ?? "N/A") ?><br>
                Phone: <?= htmlspecialchars($property['agent_phone'] ?? "N/A") ?>
            </p>
            <a href="categories.php" class="btn btn-secondary btn-sm mb-3">← Back to Categories</a>
        </div>
    </div>

    <!-- Contact Agent Form -->
    <div class="row mt-5">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4>Contact Agent</h4>
                    <form id="contactForm">
                        <input type="hidden" name="property_id" value="<?= $property['id'] ?>">
                        <div class="mb-3">
                            <label class="form-label">Your Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Your Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea name="message" rows="4" class="form-control" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <span id="btnText">Send Message</span>
                            <span id="btnSpinner" class="spinner-border spinner-border-sm d-none"></span>
                        </button>
                    </form>
                    <div id="formFeedback" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    $('#contactForm').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const feedback = $('#formFeedback');
        const btnText = $('#btnText');
        const btnSpinner = $('#btnSpinner');
        const submitBtn = form.find('button[type="submit"]');

        feedback.html('');
        submitBtn.prop('disabled', true);
        btnText.addClass('d-none');
        btnSpinner.removeClass('d-none');

        $.ajax({
            type: 'POST',
            url: '',
            data: form.serialize() + '&ajax=1',
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    feedback.html('<div class="alert alert-success">' + response.message + '</div>');
                    form[0].reset();
                } else {
                    feedback.html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                feedback.html('<div class="alert alert-danger">⚠️ Something went wrong. Try again later.</div>');
            },
            complete: function() {
                submitBtn.prop('disabled', false);
                btnText.removeClass('d-none');
                btnSpinner.addClass('d-none');
            }
        });
    });
});
</script>
</body>
</html>
