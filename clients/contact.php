<?php require_once __DIR__ . "/../includes/header.php"; ?>
<?php require_once __DIR__ . "/../includes/sidebar_client.php"; ?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $agent_id = $_POST['agent_id'];
    $message  = $_POST['message'];
    $stmt = $conn->prepare("INSERT INTO messages (client_id, agent_id, message, created_at) VALUES (?,?,?,NOW())");
    $stmt->execute([$_SESSION['user_id'], $agent_id, $message]);
    $success = "✅ Message sent!";
}
$agents = $conn->query("SELECT id, name, email FROM agents")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card shadow">
  <div class="card-header bg-info text-white">
    <h4>📩 Contact Agent</h4>
  </div>
  <div class="card-body">
    <?php if (!empty($success)): ?>
      <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <form method="post">
      <div class="mb-3">
        <label class="form-label">Select Agent</label>
        <select name="agent_id" class="form-select" required>
          <option value="">-- Choose --</option>
          <?php foreach ($agents as $a): ?>
            <option value="<?php echo $a['id']; ?>"><?php echo $a['name']; ?> (<?php echo $a['email']; ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Message</label>
        <textarea name="message" class="form-control" rows="4" required></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Send</button>
    </form>
  </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
