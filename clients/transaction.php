<?php
// clients/transaction.php
declare(strict_types=1);
session_start();
if (empty($_SESSION['client_id'])) { header('Location: login.php'); exit; }
$clientId = (int)$_SESSION['client_id'];

if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? null)) {
  http_response_code(403);
  exit('Invalid CSRF token');
}

$paymentId = isset($_POST['payment_id']) ? (int)$_POST['payment_id'] : 0;
$method    = $_POST['method'] ?? 'cash';
$reference = $_POST['reference_no'] ?? null;

if ($paymentId <= 0) { http_response_code(422); exit('payment_id required'); }

require_once __DIR__ . '/config/db.php';

// ensure the payment belongs to this client and is not already paid
$chk = $pdo->prepare("SELECT amount, status FROM rental_payments WHERE id=? AND client_id=? LIMIT 1");
$chk->execute([$paymentId, $clientId]);
$row = $chk->fetch();
if (!$row) { http_response_code(404); exit('Payment not found'); }
if ($row['status'] === 'Paid') { header('Location: rented.php?status=unpaid'); exit; }

$amount = (float)$row['amount'];

try {
  $pdo->beginTransaction();

  $ins = $pdo->prepare("INSERT INTO rental_transactions (payment_id, method, transaction_amount, reference_no)
                        VALUES (?, ?, ?, ?)");
  $ins->execute([$paymentId, $method, $amount, $reference]);

  // trigger changes status on rental_payments
  $pdo->commit();

  header('Location: rented.php?status=unpaid');
  exit;
} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  http_response_code(500);
  echo 'Payment error: ' . htmlspecialchars($e->getMessage());
}
