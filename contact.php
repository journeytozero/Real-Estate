<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . "/includes/header.php";
require_once __DIR__ . "/includes/navbar.php";

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {

    // Sanitize and trim inputs
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name  = htmlspecialchars(trim($_POST['last_name']));
    $email      = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $subject    = htmlspecialchars(trim($_POST['subject']));
    $message    = htmlspecialchars(trim($_POST['message']));

    // Validate required fields
    if (!$first_name || !$last_name || !$email || !$message) {
        $error_msg = "⚠️ Please fill in all required fields.";
    } 
    // Validate email format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "⚠️ Please enter a valid email address.";
    } 
    else {
        try {
            // Check for duplicate message from the same email today
            $checkStmt = $pdo->prepare("SELECT id FROM contact_messages 
                                        WHERE email = :email 
                                        AND DATE(created_at) = CURDATE() 
                                        LIMIT 1");
            $checkStmt->execute([':email' => $email]);

            if ($checkStmt->rowCount() > 0) {
                $error_msg = "⚠️ You have already sent a message today. Please try again tomorrow.";
            } else {
                // Insert new message
                $insertStmt = $pdo->prepare(
                    "INSERT INTO contact_messages 
                     (first_name, last_name, email, subject, message) 
                     VALUES (:first_name, :last_name, :email, :subject, :message)"
                );

                $insertStmt->execute([
                    ':first_name' => $first_name,
                    ':last_name'  => $last_name,
                    ':email'      => $email,
                    ':subject'    => $subject,
                    ':message'    => $message
                ]);

                $success_msg = "✅ Your message has been sent successfully!";
            }
        } catch (PDOException $e) {
            $error_msg = "❌ Database error: " . $e->getMessage();
        }
    }
}
?>
<div class="container py-5">
    <div class="page-header text-center mb-5">
        <h1>Let’s talk about everything</h1>
        <p class="text-muted">We’d love to hear from you — feel free to reach out using the form below or our contact details.</p>
    </div>

    <div class="row g-4">
        <!-- Contact Info -->
        <div class="col-md-5">
            <div class="contact-info bg-light p-4 rounded shadow-sm">
                <h4 class="mb-4">Contact Information</h4>
                <p><strong>Address:</strong><br>SKS Tower, Road 45<br> Mohakhali, Dhaka 1212, Bangladesh</p>
                <p><strong>Email:</strong><br>contact@yourcompany.com</p>
                <p><strong>Phone:</strong><br>+1 (123) 456-7890</p>
                <h6 class="mt-4">Our Location</h6>
                <iframe src=https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d20653.101053835468!2d90.3718464745503!3d23.783478970289526!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755c769ad5e7f6f%3A0x1d928a50d9cbcc90!2sSKS%20Shopping%20Mall!5e0!3m2!1sen!2sbd!4v1757932159144!5m2!1sen!2sbd" height="250" style="border:0; width:100%;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="col-md-7">
            <div class="contact-info bg-white p-4 rounded shadow-sm">
                <h4 class="mb-4">Send Us a Message</h4>

                <!-- Display Messages -->
                <?php if($success_msg): ?>
                    <div class="alert alert-success"><?= $success_msg ?></div>
                <?php endif; ?>
                <?php if($error_msg): ?>
                    <div class="alert alert-danger"><?= $error_msg ?></div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="John" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Doe" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Your Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com" required>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject">
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" placeholder="Type your message here..." required></textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" name="send_message" class="btn btn-primary btn-lg">Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/includes/footer.php"; ?>
