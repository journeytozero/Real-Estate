<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../partials/db.php"; // PDO connection

// Only allow admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$name = $email = $phone = $photo = $document = '';

// Fetch existing data if editing
if ($id) {
    $stmt = $conn->prepare("SELECT * FROM agents WHERE id = ?");
    $stmt->execute([$id]);
    $agent = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($agent) {
        $name     = $agent['name'];
        $email    = $agent['email'];
        $phone    = $agent['phone'];
        $photo    = $agent['photo'];
        $document = $agent['document'];
    } else {
        header("Location: idx.php?page=agents");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    // File upload handling
    $uploadDir = __DIR__ . '/../uploads/agents/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    // Photo upload
    if (!empty($_FILES['photo']['name'])) {
        $photoName = time() . '_' . basename($_FILES['photo']['name']);
        $photoPath = $uploadDir . $photoName;
        move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath);
    } else {
        $photoName = $photo; // keep existing if not uploading new
    }

    // Document upload
    if (!empty($_FILES['document']['name'])) {
        $docName = time() . '_' . basename($_FILES['document']['name']);
        $docPath = $uploadDir . $docName;
        move_uploaded_file($_FILES['document']['tmp_name'], $docPath);
    } else {
        $docName = $document;
    }

    // Check for duplicate email
    $stmt = $conn->prepare($id
        ? "SELECT id FROM agents WHERE email = ? AND id != ?"
        : "SELECT id FROM agents WHERE email = ?"
    );
    $id ? $stmt->execute([$email, $id]) : $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        echo "<div class='alert alert-danger'>Email already exists!</div>";
    } else {
        $sql = $id
            ? "UPDATE agents SET name = ?, email = ?, phone = ?, photo = ?, document = ? WHERE id = ?"
            : "INSERT INTO agents (name, email, phone, photo, document) VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $id
            ? $stmt->execute([$name, $email, $phone, $photoName, $docName, $id])
            : $stmt->execute([$name, $email, $phone, $photoName, $docName]);

        header("Location: idx.php?page=agents");
        exit;
    }
}
?>

<div class="container p-4">
  <h3><?= $id ? "Edit Agent" : "Add Agent" ?></h3>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label>Name</label>
      <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" required>
    </div>
    <div class="mb-3">
      <label>Email</label>
      <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
    </div>
    <div class="mb-3">
      <label>Phone</label>
      <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($phone) ?>" required>
    </div>
    <div class="mb-3">
      <label>Photo</label>
      <input type="file" name="photo" class="form-control" accept="image/*">
      <?php if ($photo): ?>
        <img src="../uploads/agents/<?= htmlspecialchars($photo) ?>" width="100" class="mt-2">
      <?php endif; ?>
    </div>
    <div class="mb-3">
      <label>Document</label>
      <input type="file" name="document" class="form-control" accept=".pdf,.doc,.docx">
      <?php if ($document): ?>
        <a href="../uploads/agents/<?= htmlspecialchars($document) ?>" target="_blank" class="d-block mt-2">View existing document</a>
      <?php endif; ?>
    </div>
    <button class="btn btn-success"><?= $id ? "Update" : "Save" ?></button>
    <a href="idx.php?page=agents" class="btn btn-secondary">Cancel</a>
  </form>
</div>
