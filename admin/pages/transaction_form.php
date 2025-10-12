<?php
// assumes $conn (PDO) is available and session started above

$id = $_GET['id'] ?? null;
$property_id = $client_id = $agent_id = $status = $transaction_date = "";
$amount = $payment_amount = "";

// If editing
if ($id) {
    $stmt = $conn->prepare("SELECT * FROM transactions WHERE id = ?");
    $stmt->execute([$id]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($transaction) {
        // brings in: property_id, client_id, agent_id, amount, status, date, payment_amount (if exists)
        extract($transaction); // careful: sets $date; we'll map it to $transaction_date below
        $transaction_date = $transaction['date'] ?? $transaction_date;
        // if legacy rows don’t have payment_amount, fallback to amount
        if (!isset($transaction['payment_amount'])) {
            $payment_amount = $amount;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $property_id      = $_POST['property_id'];
    $client_id        = $_POST['client_id'];
    $agent_id         = $_POST['agent_id'];
    $amount           = (string)($_POST['amount'] ?? '0');
    $payment_amount   = (string)($_POST['payment_amount'] ?? '0');
    $status           = $_POST['status'];
    $transaction_date = $_POST['transaction_date'];

    // normalize numbers and basic server-side guard
    $amount_f         = max(0, (float)$amount);
    $payment_amount_f = max(0, (float)$payment_amount);
    if ($payment_amount_f > $amount_f) {
        $payment_amount_f = $amount_f; // cap to amount
    }

    if ($id) {
        // Update transaction
        $upd = $conn->prepare("
            UPDATE transactions 
               SET property_id = ?, 
                   client_id   = ?, 
                   agent_id    = ?, 
                   amount      = ?, 
                   payment_amount = ?, 
                   status      = ?, 
                   date        = ? 
             WHERE id = ?
        ");
        $upd->execute([
            $property_id, $client_id, $agent_id,
            $amount_f, $payment_amount_f, $status,
            $transaction_date, $id
        ]);
        $_SESSION['success'] = "✅ Transaction updated!";
    } else {
        // Insert new transaction
        $ins = $conn->prepare("
            INSERT INTO transactions 
                (property_id, client_id, agent_id, amount, payment_amount, status, date) 
            VALUES (?,?,?,?,?,?,?)
        ");
        $ins->execute([
            $property_id, $client_id, $agent_id,
            $amount_f, $payment_amount_f, $status,
            $transaction_date
        ]);
        $_SESSION['success'] = "✅ Transaction added!";
    }

    // 🔥 Auto update property status (same as before)
    if ($status === 'Completed') {
        $conn->prepare("UPDATE properties SET status = 'Sold' WHERE id = ?")->execute([$property_id]);
    } elseif ($status === 'Cancelled') {
        $conn->prepare("UPDATE properties SET status = 'Available' WHERE id = ?")->execute([$property_id]);
    }

    header("Location: idx.php?page=transactions");
    exit;
}
?>

<div class="container p-4">
  <h3><?= $id ? "Edit Transaction" : "Add Transaction" ?></h3>
  <form method="POST" id="transactionForm">

    <!-- Property -->
    <div class="mb-3">
      <label class="form-label">Property</label>
      <select name="property_id" id="propertySelect" class="form-select" required>
        <option value="">Select Property</option>
        <?php
        // Pull price too so we can auto-fill amount
        $props = $conn->query("SELECT id, name, status, price FROM properties ORDER BY name");
        while ($p = $props->fetch(PDO::FETCH_ASSOC)): 
            $selected = ($p['id'] == $property_id) ? 'selected' : '';
            ?>
          <option 
            value="<?= $p['id'] ?>" 
            <?= $selected ?>
            data-price="<?= htmlspecialchars((string)$p['price'], ENT_QUOTES) ?>">
            <?= htmlspecialchars($p['name']) ?> (<?= htmlspecialchars($p['status']) ?>) — $<?= number_format((float)$p['price'], 2) ?>
          </option>
        <?php endwhile; ?>
      </select>
      <div class="form-text">Amount auto-fills from the selected property’s price.</div>
    </div>

    <!-- Client -->
    <div class="mb-3">
      <label class="form-label">Client</label>
      <select name="client_id" class="form-select" required>
        <option value="">Select Client</option>
        <?php
        $clients = $conn->query("SELECT id, name, email FROM clients ORDER BY name");
        while($c = $clients->fetch(PDO::FETCH_ASSOC)): ?>
          <option value="<?= $c['id'] ?>" <?= ($c['id']==$client_id)?'selected':'' ?>>
            <?= htmlspecialchars($c['name']) ?> (<?= htmlspecialchars($c['email']) ?>)
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <!-- Agent -->
    <div class="mb-3">
      <label class="form-label">Agent</label>
      <select name="agent_id" class="form-select" required>
        <option value="">Select Agent</option>
        <?php
        $agents = $conn->query("SELECT id, name, email FROM agents ORDER BY name");
        while($a = $agents->fetch(PDO::FETCH_ASSOC)): ?>
          <option value="<?= $a['id'] ?>" <?= ($a['id']==$agent_id)?'selected':'' ?>>
            <?= htmlspecialchars($a['name']) ?> (<?= htmlspecialchars($a['email']) ?>)
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <!-- Amount (auto) -->
    <div class="mb-3">
      <label class="form-label">Amount (auto from property)</label>
      <input 
        type="number" 
        name="amount" 
        id="amountField" 
        class="form-control" 
        step="0.01" 
        min="0" 
        value="<?= htmlspecialchars((string)$amount, ENT_QUOTES) ?>" 
        readonly
        required>
    </div>

    <!-- Payment Amount (new) -->
    <div class="mb-3">
      <label class="form-label">Payment Amount</label>
      <input 
        type="number" 
        name="payment_amount" 
        id="paymentAmount" 
        class="form-control" 
        step="0.01" 
        min="0" 
        value="<?= htmlspecialchars($payment_amount !== "" ? (string)$payment_amount : (string)$amount, ENT_QUOTES) ?>"
        required>
      <div class="form-text">Cannot exceed the Amount.</div>
    </div>

    <!-- Status -->
    <div class="mb-3">
      <label class="form-label">Status</label>
      <select name="status" class="form-select">
        <option value="Pending"   <?= $status==='Pending'?'selected':'' ?>>Pending</option>
        <option value="Completed" <?= $status==='Completed'?'selected':'' ?>>Completed</option>
        <option value="Cancelled" <?= $status==='Cancelled'?'selected':'' ?>>Cancelled</option>
      </select>
    </div>

    <!-- Transaction Date -->
    <div class="mb-3">
      <label class="form-label">Transaction Date</label>
      <input type="date" name="transaction_date" class="form-control" value="<?= htmlspecialchars((string)$transaction_date, ENT_QUOTES) ?>" required>
    </div>

    <button class="btn btn-success">Save</button>
    <a href="idx.php?page=transactions" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<script>
// Auto-fill Amount from selected property's data-price,
// and keep Payment Amount <= Amount.
(function(){
  const isEdit = <?= $id ? 'true' : 'false' ?>;
  const sel    = document.getElementById('propertySelect');
  const amount = document.getElementById('amountField');
  const pay    = document.getElementById('paymentAmount');

  function toNumber(v){ return Math.max(0, parseFloat(v || 0)); }

  function applyPriceFromSelection(force = false){
    const opt = sel.options[sel.selectedIndex];
    if (!opt) return;
    const price = toNumber(opt.getAttribute('data-price'));
    if (!isEdit || force){ // on add, always set; on edit you can pass force=true after property change
      amount.value = price.toFixed(2);
      // if payment empty or greater than amount, set to amount
      if (!pay.value || toNumber(pay.value) > price) {
        pay.value = price.toFixed(2);
      }
      pay.setAttribute('max', price.toFixed(2));
    }
  }

  // When property changes, always update
  sel.addEventListener('change', function(){
    applyPriceFromSelection(true);
  });

  // Keep payment <= amount
  pay.addEventListener('input', function(){
    const a = toNumber(amount.value);
    const p = toNumber(pay.value);
    if (p > a) pay.value = a.toFixed(2);
  });

  // On load:
  // - If creating new: set from current selection (if any)
  // - If editing: respect existing values; still cap payment to amount
  window.addEventListener('DOMContentLoaded', function(){
    if (!isEdit) applyPriceFromSelection(true);
    // ensure max cap applied
    const a = toNumber(amount.value);
    if (a > 0) pay.setAttribute('max', a.toFixed(2));
    if (toNumber(pay.value) > a) pay.value = a.toFixed(2);
  });
})();
</script>
