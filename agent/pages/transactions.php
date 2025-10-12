<?php
session_start();
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../includes/header.php";
require_once __DIR__ . "/../includes/sidebar.php";
require_once __DIR__ . "/../includes/navbar.php";

if (!isset($_SESSION['agent_id'])) {
    header("Location: ../login.php");
    exit;
}

$agent_id = $_SESSION['agent_id'];

$properties = $conn->query("SELECT id, name, location, price FROM properties WHERE status != 'Rented'")->fetchAll(PDO::FETCH_ASSOC);
$clients    = $conn->query("SELECT id, name FROM clients ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $conn->prepare("INSERT INTO rented (property_id, agent_id, client_id, category, rented_start_date, rented_end_date) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['property_id'],
        $agent_id,
        $_POST['client_id'],
        $_POST['category'],
        $_POST['rent_start'],
        $_POST['rent_end']
    ]);
    $rented_id = $conn->lastInsertId();

    $stmt = $conn->prepare("UPDATE properties SET status = 'Rented' WHERE id = ?");
    $stmt->execute([$_POST['property_id']]);

    if (!empty($_FILES['photos']['name'][0])) {
        $uploadDir = __DIR__ . "/../uploads/rented/";
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

        foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
            $fileName   = time() . "_" . basename($_FILES['photos']['name'][$key]);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($tmp_name, $targetPath)) {
                $stmt = $conn->prepare("INSERT INTO rented_photos (rented_id, photo_path) VALUES (?, ?)");
                $stmt->execute([$rented_id, "uploads/rented/" . $fileName]);
            }
        }
    }

    $_SESSION['success'] = "Transaction added successfully!";
    header("Location: transaction.php");
    exit;
}

$stmt = $conn->prepare("
    SELECT r.id AS rented_id, p.name AS property_name, p.location, p.price, r.category, 
           c.name AS client_name, r.rented_start_date, r.rented_end_date, r.created_at
    FROM rented r
    JOIN properties p ON r.property_id = p.id
    JOIN clients c ON r.client_id = c.id
    WHERE r.agent_id = ?
    ORDER BY r.created_at DESC
");
$stmt->execute([$agent_id]);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content">
    <h2 class="mb-4">Transactions</h2>

    <!-- Add Transaction Button -->
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#transactionModal">
        <i class="fas fa-plus"></i> Add Transaction
    </button>

    <!-- Transaction Modal -->
    <div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="transactionModalLabel">Add New Transaction</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Select Property</label>
                            <select name="property_id" class="form-control" required>
                                <option value="">-- Select Property --</option>
                                <?php foreach ($properties as $p): ?>
                                    <option value="<?= $p['id'] ?>">
                                        <?= htmlspecialchars($p['name']) ?> (<?= htmlspecialchars($p['location']) ?>) - $<?= number_format($p['price'], 2) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Select Client</label>
                            <select name="client_id" class="form-control" required>
                                <option value="">-- Select Client --</option>
                                <?php foreach ($clients as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-control" required>
                                <option value="">-- Select Category --</option>
                                <option value="Apartment A">Apartment A</option>
                                <option value="Apartment B">Apartment B</option>
                                <option value="House A">House A</option>
                                <option value="House B">House B</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Rent Start Date</label>
                            <input type="date" name="rent_start" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Rent End Date</label>
                            <input type="date" name="rent_end" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Upload Photos</label>
                            <input type="file" name="photos[]" class="form-control" multiple accept="image/*">
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-success">Save Transaction</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions List -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">Transaction History</div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Property</th>
                        <th>Client</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>Price</th>
                        <th>Rent Start</th>
                        <th>Rent End</th>
                        <th>Transaction Date</th>
                        <th>Action</th> <!-- New column -->
                    </tr>
                </thead>
                <tbody>
                    <?php if ($transactions): ?>
                        <?php foreach ($transactions as $t): ?>
                            <tr>
                                <td><?= htmlspecialchars($t['property_name']) ?></td>
                                <td><?= htmlspecialchars($t['client_name']) ?></td>
                                <td><?= htmlspecialchars($t['category']) ?></td>
                                <td><?= htmlspecialchars($t['location']) ?></td>
                                <td>$<?= number_format($t['price'], 2) ?></td>
                                <td><?= htmlspecialchars($t['rented_start_date']) ?></td>
                                <td><?= $t['rented_end_date'] ? htmlspecialchars($t['rented_end_date']) : 'Ongoing' ?></td>
                                <td><?= date("M d, Y", strtotime($t['created_at'])) ?></td>
                                <td>
                                    <a href="print_invoice.php?rented_id=<?= $t['rented_id'] ?>" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="fas fa-print"></i> Print
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center">No transactions found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>