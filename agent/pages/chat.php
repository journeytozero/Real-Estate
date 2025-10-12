<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['agent_id'])) {
    header("Location: ../login.php");
    exit;
}

$agentId  = (int)$_SESSION['agent_id'];
$clientId = isset($_GET['client_id']) ? (int)$_GET['client_id'] : 0;

// Handle AJAX requests
if (isset($_GET['action']) && $clientId > 0) {
    header('Content-Type: application/json');

    // Fetch messages
    if ($_GET['action'] === 'fetch') {
        $stmt = $conn->prepare("
            SELECT sender, message, created_at
            FROM chat_messages
            WHERE client_id = ? AND agent_id = ?
            ORDER BY created_at ASC
        ");
        $stmt->execute([$clientId, $agentId]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($messages);
        exit;
    }

    // Send message
    if ($_GET['action'] === 'send' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $message = trim($_POST['message'] ?? '');
        if ($message !== '') {
            $stmt = $conn->prepare("
                INSERT INTO chat_messages (client_id, agent_id, sender, message)
                VALUES (?, ?, 'agent', ?)
            ");
            $stmt->execute([$clientId, $agentId, $message]);
        }
        echo json_encode(['status' => 'ok']);
        exit;
    }
}

// Fetch clients list
$clients = $conn->query("SELECT id, name FROM clients ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . "/../includes/header.php";
require_once __DIR__ . "/../includes/sidebar.php";
require_once __DIR__ . "/../includes/navbar.php";
?>

<div class="content">
    <h4 class="mb-3">Chat with Client</h4>

    <form method="GET">
        <select name="client_id" class="form-select mb-3" onchange="this.form.submit()">
            <option value="">-- Select Client --</option>
            <?php foreach ($clients as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $c['id']==$clientId?'selected':'' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if ($clientId): ?>
        <div class="chat-box border rounded p-3 mb-3"
             id="chatBox"
             style="height:300px; overflow-y:auto; background:#f9f9f9;">
        </div>

        <form id="chatForm">
            <div class="input-group">
                <input type="text" name="message" id="messageInput" class="form-control" placeholder="Type a message..." required>
                <button class="btn btn-success">Send</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php if ($clientId): ?>
<script>
function loadChat() {
    $.get("chat.php", { client_id: <?= $clientId ?>, action: "fetch" }, function(data) {
        let html = "";
        data.forEach(msg => {
            let side  = (msg.sender === 'agent') ? 'text-end' : 'text-start';
            let badge = (msg.sender === 'agent') ? 'success' : 'secondary';
            html += `
                <div class="mb-2 ${side}">
                    <span class="badge bg-${badge}">${msg.sender.charAt(0).toUpperCase() + msg.sender.slice(1)}</span>
                    <p class="d-inline-block mb-0">${$('<div>').text(msg.message).html()}</p>
                    <small class="text-muted d-block">${msg.created_at}</small>
                </div>
            `;
        });
        $("#chatBox").html(html);
        $("#chatBox").scrollTop($("#chatBox")[0].scrollHeight);
    }, "json");
}

// Auto-refresh every 3s
setInterval(loadChat, 3000);
loadChat();

// Send message
$("#chatForm").on("submit", function(e) {
    e.preventDefault();
    let msg = $("#messageInput").val().trim();
    if (!msg) return;

    $.post("chat.php?action=send&client_id=<?= $clientId ?>", { message: msg }, function() {
        $("#messageInput").val("");
        loadChat(); // refresh immediately
    }, "json");
});
</script>
<?php endif; ?>
