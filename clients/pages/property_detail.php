<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

// Property ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: properties.php");
    exit;
}

// Fetch property
$sql = "
    SELECT p.*, a.name AS agent_name, a.email AS agent_email, a.phone AS agent_phone
    FROM properties p
    LEFT JOIN agents a ON p.agent_id = a.id
    WHERE p.id = ?
";
$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    header("Location: properties.php");
    exit;
}

// Fetch photos
$photos = [];
$photoStmt = $conn->prepare("SELECT photo FROM property_photos WHERE property_id = ?");
$photoStmt->execute([$id]);
$photos = $photoStmt->fetchAll(PDO::FETCH_COLUMN);

// Check if saved
$isSaved = false;
if (isset($_SESSION['client_id'])) {
    $chk = $conn->prepare("SELECT 1 FROM saved_properties WHERE client_id = ? AND property_id = ?");
    $chk->execute([$_SESSION['client_id'], $id]);
    $isSaved = $chk->fetchColumn() ? true : false;
}

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/sidebar.php';
require_once __DIR__ . '/../partials/navbar.php';
?>

<div class="content">
    <div class="row">
        <!-- Gallery -->
        <div class="col-md-7 mb-4">
            <div class="card shadow-sm">
                <?php if ($photos): ?>
                    <img src="../../admin/uploads/<?= htmlspecialchars($photos[0]) ?>" class="card-img-top" style="height:400px;object-fit:cover;">
                    <div class="d-flex flex-wrap p-2">
                        <?php foreach ($photos as $ph): ?>
                            <img src="../../admin/uploads/<?= htmlspecialchars($ph) ?>" class="m-1 rounded" style="width:100px;height:80px;object-fit:cover;cursor:pointer;" onclick="document.getElementById('mainPhoto').src=this.src;">
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <img src="https://via.placeholder.com/600x400?text=No+Image" class="card-img-top">
                <?php endif; ?>
            </div>
        </div>

        <!-- Property Info -->
        <div class="col-md-5 mb-4">
            <div class="card shadow-sm p-3 h-100">
                <h3 class="mb-3"><?= htmlspecialchars($property['name']) ?></h3>
                <p class="text-muted"><i class="fas fa-map-marker-alt me-1"></i> <?= htmlspecialchars($property['location']) ?></p>
                <p class="fw-bold text-primary fs-4"><?= number_format($property['price']) ?> TK</p>
                <p>Status: 
                    <span class="badge bg-<?= $property['status']=='available'?'success':($property['status']=='pending'?'warning':'danger') ?>">
                        <?= ucfirst($property['status']) ?>
                    </span>
                </p>
                <p><?= nl2br(htmlspecialchars($property['description'])) ?></p>

                <div class="d-flex gap-2 mt-auto">
                    <button class="btn <?= $isSaved ? 'btn-danger' : 'btn-outline-danger' ?> save-btn" data-id="<?= $property['id'] ?>">
                        <i class="fa-solid fa-heart"></i></i> <?= $isSaved ? 'Saved' : 'Save' ?>
                    </button>
                    <a href="compare.php?ids=<?= $property['id'] ?>" class="btn btn-outline-primary">
                        <i class="fas fa-balance-scale"></i> Compare
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Agent Info -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light"><strong>Agent Information</strong></div>
        <div class="card-body">
            <p><strong><?= htmlspecialchars($property['agent_name'] ?? 'N/A') ?></strong></p>
            <p><i class="fas fa-envelope me-1"></i> <?= htmlspecialchars($property['agent_email'] ?? 'Not provided') ?></p>
            <p><i class="fas fa-phone me-1"></i> <?= htmlspecialchars($property['agent_phone'] ?? 'Not provided') ?></p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// ✅ Save property toggle
$(document).on("click", ".save-btn", function() {
    let id = $(this).data("id");
    let btn = $(this);
    $.post("../save_property.php", { property_id: id }, function(res) {
        if (res === "login") {
            alert("Please login to save properties.");
            window.location.href = "../login.php";
        } else if (res === "saved") {
            btn.removeClass("btn-outline-danger").addClass("btn-danger").text("Saved");
        } else if (res === "unsaved") {
            btn.removeClass("btn-danger").addClass("btn-outline-danger").text("Save");
        }
    });
});
</script>
