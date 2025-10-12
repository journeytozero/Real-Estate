<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$id    = $_GET['id'] ?? null;
$name  = '';
$email = '';
$phone = '';
$error = '';

if ($id) {
    $stmt = $conn->prepare("SELECT * FROM clients WHERE id=?");
    $stmt->execute([$id]);
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $name  = $row['name'];
        $email = $row['email'];
        $phone = $row['phone'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    if ($id) {
        // check duplicate email except self
        $chk = $conn->prepare("SELECT id FROM clients WHERE email=? AND id<>?");
        $chk->execute([$email, $id]);
        if ($chk->rowCount() > 0) {
            $error = "Email already exists!";
        } else {
            $upd = $conn->prepare("UPDATE clients SET name=?, email=?, phone=? WHERE id=?");
            $upd->execute([$name, $email, $phone, $id]);
            $_SESSION['success'] = "✅ Client updated successfully!";
            header("Location: index.php?page=clients"); exit;
        }
    } else {
        $chk = $conn->prepare("SELECT id FROM clients WHERE email=?");
        $chk->execute([$email]);
        if ($chk->rowCount() > 0) {
            $error = "Email already exists!";
        } else {
            $ins = $conn->prepare("INSERT INTO clients (name,email,phone) VALUES (?,?,?)");
            $ins->execute([$name, $email, $phone]);
            $_SESSION['success'] = "✅ Client added successfully!";
            header("Location: index.php?page=clients"); exit;
        }
    }
}
?>

<div class="container p-4">
  <h3><?= $id ? "Edit Client" : "Add Client" ?></h3>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Phone</label>
      <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($phone) ?>" required>
    </div>
    <button class="btn btn-success">Save</button>
    <a href="idx.php?page=clients" class="btn btn-secondary">Cancel</a>
  </form>
</div>
