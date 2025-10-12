<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

// ✅ Detect role
$role     = null;
$agentId  = 0;
$clientId = 0;

if (isset($_SESSION['agent_id'])) {
    $role = 'agent';
    $agentId = (int)$_SESSION['agent_id'];
    $clientId = isset($_GET['client_id']) ? (int)$_GET['client_id'] : 0;
} elseif (isset($_SESSION['client_id'])) {
    $role = 'client';
    $clientId = (int)$_SESSION['client_id'];
    $agentId = isset($_GET['agent_id']) ? (int)$_GET['agent_id'] : 0;
} else {
    header("Location: ../login.php");
    exit;
}

// ✅ Handle AJAX requests
if (isset($_GET['action']) && $agentId > 0 && $clientId > 0) {
    header('Content-Type: application/json');

    // Fetch messages
    if ($_GET['action'] === 'fetch') {
        $stmt = $conn->prepare("
            SELECT sender, receiver, message, created_at
            FROM chat_messages
            WHERE client_id = ? AND agent_id = ?
            ORDER BY created_at ASC
        ");
        $stmt->execute([$clientId, $agentId]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Mark messages as read
        $stmt2 = $conn->prepare("
            UPDATE chat_messages
            SET is_read = 1
            WHERE client_id = ? AND agent_id = ? AND receiver = ? AND is_read = 0
        ");
        $stmt2->execute([$clientId, $agentId, $role]);

        echo json_encode($messages);
        exit;
    }

    // Send message
    if ($_GET['action'] === 'send' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $message = trim($_POST['message'] ?? '');
        if ($message !== '' && $role) {
            try {
                $receiver = ($role === 'client') ? 'agent' : 'client';
                $stmt = $conn->prepare("
                    INSERT INTO chat_messages (client_id, agent_id, sender, receiver, message)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$clientId, $agentId, $role, $receiver, $message]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                exit;
            }
        }
        echo json_encode(['status' => 'ok']);
        exit;
    }
}

// ✅ Dropdown list
if ($role === 'agent') {
    $list = $conn->query("SELECT id, name FROM clients ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    $targetType = 'client';
    $targetId   = $clientId;
    $btnColor   = 'success';
} elseif ($role === 'client') {
    $list = $conn->query("SELECT id, name FROM agents ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    $targetType = 'agent';
    $targetId   = $agentId;
    $btnColor   = 'primary';
}

// Includes
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/sidebar.php';
require_once __DIR__ . '/../partials/navbar.php';

// ✅ Base URL (adjust if needed)
$baseUrl = "/RS/clients/pages/chat.php";
?>

<div class="content">
    <h4 class="mb-3">Chat</h4>

    <!-- Dropdown -->
    <form method="GET">
        <select name="<?= $targetType ?>_id" class="form-select mb-3" onchange="this.form.submit()">
            <option value="">-- Select <?= ucfirst($targetType) ?> --</option>
            <?php foreach ($list as $row): ?>
                <option value="<?= $row['id'] ?>" <?= $row['id']==$targetId ? 'selected' : '' ?>>
                    <?= htmlspecialchars($row['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if ($agentId && $clientId): ?>
        <!-- Chat box -->
        <div class="chat-box border rounded p-3 mb-3"
             id="chatBox"
             style="height:300px; overflow-y:auto; background:#f9f9f9;">
        </div>

        <!-- Message input -->
        <form id="chatForm">
            <div class="input-group">
                <input type="text" name="message" id="messageInput" class="form-control" placeholder="Type a message..." required>
                <button type="submit" class="btn btn-<?= $btnColor ?>">Send</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php if ($agentId && $clientId): ?>
<script>
const BASE_URL = "<?= $baseUrl ?>";

// ✅ Load chat messages
function loadChat() {
    $.get(BASE_URL, { agent_id: <?= $agentId ?>, client_id: <?= $clientId ?>, action: "fetch" }, function(data) {
        let html = "";
        data.forEach(msg => {
            let side  = (msg.sender === '<?= $role ?>') ? 'text-end' : 'text-start';
            let badge = (msg.sender === 'agent') ? 'success' : 'primary';
            html += `
                <div class="mb-2 ${side}">
                    <span class="badge bg-${badge}">
                        ${msg.sender.charAt(0).toUpperCase() + msg.sender.slice(1)}
                    </span>
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

// ✅ Send message via AJAX
$("#chatForm").on("submit", function(e) {
    e.preventDefault();
    let msg = $("#messageInput").val().trim();
    if (!msg) return;

    $.ajax({
        url: BASE_URL + "?action=send&agent_id=<?= $agentId ?>&client_id=<?= $clientId ?>",
        type: "POST",
        data: { message: msg },
        dataType: "json",
        success: function(res) {
            if (res.status === "ok") {
                $("#messageInput").val(""); // clear input
                loadChat(); // refresh immediately
            } else {
                alert("Error: " + res.message);
            }
        },
        error: function(xhr, status, err) {
            console.error("Send failed:", xhr.responseText);
            alert("Failed to send message. Check console for details.");
        }
    });
});
</script>
<?php endif; ?>
