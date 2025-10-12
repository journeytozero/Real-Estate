<?php
// Start session and check login
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../partials/db.php";

// Admin info
$admin_name   = $_SESSION['admin_name'] ?? 'Admin';
$profilePhoto = $_SESSION['admin_photo'] ?? 'default.png';

// Fetch agents
$agents = $conn->query("SELECT id, name, email FROM agents ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 border-end" style="height:100vh; overflow-y:auto;">
      <div class="text-center mb-3 mt-3">
        <img src="uploads/<?= htmlspecialchars($profilePhoto) ?>" class="rounded-circle mb-2" width="80" height="80" alt="Profile">
        <h6><?= htmlspecialchars($admin_name) ?></h6>
        <span class="text-primary small">Administrator</span>
      </div>
      <h6 class="ps-2 mt-3">Agents</h6>
      <ul class="list-group" id="chatUserList">
        <?php foreach ($agents as $a): ?>
          <li class="list-group-item list-group-item-action"
              data-email="<?= htmlspecialchars($a['email']) ?>"
              id="user_agent_<?= $a['id'] ?>">
              <?= htmlspecialchars($a['name']) ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <!-- Chat/Email Panel -->
    <div class="col-md-9 d-flex flex-column">
      <div class="border rounded p-2 flex-grow-1 overflow-auto" 
           id="chatMessages" 
           style="height:calc(100vh - 150px); background:#f8f9fa;">
        <div class="text-center text-muted mt-3">Select an agent to start messaging</div>
      </div>
      <div class="input-group mt-2">
        <input type="text" id="chatSubject" class="form-control" placeholder="Subject" disabled>
        <input type="text" id="chatInput" class="form-control" placeholder="Type your message..." disabled>
        <button class="btn btn-primary" id="sendChat" disabled>Send</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function(){
    let currentEmail = '';

    // Agent selection
    $('#chatUserList').on('click','li',function(){
        $('#chatUserList li').removeClass('active');
        $(this).addClass('active');
        currentEmail = $(this).data('email');

        // Enable form
        $('#chatInput, #chatSubject, #sendChat').prop('disabled', false);

        $('#chatMessages').html(
            '<div class="text-center text-muted mt-3">Now messaging: <strong>'+currentEmail+'</strong></div>'
        );
    });

    // Send message
    $('#sendChat').click(function(){
        const subject = $('#chatSubject').val().trim();
        const message = $('#chatInput').val().trim();

        if(!message || !currentEmail){
            alert('Please select an agent and enter a message.');
            return;
        }

        $('#sendChat').prop('disabled', true).text('Sending...');

        $.post('send_email.php',
            { to: currentEmail, subject: subject, message: message },
            function(res){
                try {
                    const data = JSON.parse(res);
                    if(data.status === 'ok'){
                        $('#chatMessages').append(
                            '<div class="text-end mb-1"><small><strong>Admin</strong></small>: '+
                            $('<div>').text(message).html()+'</div>'
                        );
                        $('#chatInput').val('');
                        $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
                    } else {
                        alert('Error: ' + data.message);
                    }
                } catch(e){
                    alert('Unexpected response from server.');
                }
                $('#sendChat').prop('disabled', false).text('Send');
            }
        ).fail(function(){
            alert('Failed to send request. Please check server.');
            $('#sendChat').prop('disabled', false).text('Send');
        });
    });

    // Enter = send
    $('#chatInput').keypress(function(e){
        if(e.which === 13){
            e.preventDefault();
            $('#sendChat').click();
        }
    });
});
</script>
