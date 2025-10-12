<?php
require_once __DIR__ . "/config/db.php";
// require_once __DIR__ . "/includes/header.php";
// include __DIR__ . '/includes/navbar.php';

// Fetch all agents
$stmt = $conn->query("SELECT * FROM agents ORDER BY id DESC");
$agents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">
    <h2 class="mb-5 text-center fw-bold" style="font-family: 'Poppins', sans-serif; color:#0d6efd;">Meet Our Professional Agents</h2>
    <div class="row g-4">
        <?php foreach($agents as $agent):
            $photo = !empty($agent['photo']) ? "../RS/admin/uploads/agents/" . htmlspecialchars($agent['photo']) : "https://via.placeholder.com/250";
            $email = htmlspecialchars($agent['email']);
            $phone = htmlspecialchars($agent['phone'] ?? '');
            $whatsapp = preg_replace('/\D/', '', $phone); // numeric for WhatsApp link
        ?>
        <div class="col-lg-4 col-md-6">
            <div class="agent-card h-100 shadow-lg rounded-4 overflow-hidden position-relative">
                <div class="agent-img-wrapper">
                    <img src="<?= $photo ?>" alt="<?= htmlspecialchars($agent['name']) ?>" class="agent-img">
                </div>
                <div class="agent-info text-center p-4">
                    <h5 class="fw-bold mb-2"><?= htmlspecialchars($agent['name']) ?></h5>
                    <p class="mb-1"><i class="bi bi-envelope me-2"></i><?= $email ?></p>
                    <p class="mb-1"><i class="fa-solid fa-phone-volume"></i><?= $phone ?: '-' ?></p>
                    <p class="mb-2"><i class="bi bi-card-checklist me-2"></i><?= htmlspecialchars($agent['trade_license'] ?? '-') ?></p>

                    <?php if(!empty($agent['document'])): ?>
                        <a href="uploads/documents/<?= htmlspecialchars($agent['document']) ?>" target="_blank" class="btn btn-outline-primary btn-sm mb-2">View Document</a>
                    <?php endif; ?>

                    <!-- Contact Buttons -->
                    <div class="d-flex justify-content-center gap-2 mt-2">
                        <?php if(!empty($email)): ?>
                            <a href="mailto:<?= $email ?>" class="btn btn-sm btn-outline-primary" title="Email"><i class="fa-regular fa-envelope"></i></a>
                        <?php endif; ?>
                        <?php if(!empty($phone)): ?>
                            <a href="tel:<?= $phone ?>" class="btn btn-sm btn-outline-success" title="Call"><i class="fa-solid fa-headset"></i></a>
                            <a href="https://wa.me/<?= $whatsapp ?>" target="_blank" class="btn btn-sm btn-outline-success" title="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<!-- <?php include __DIR__ . '/includes/footer.php'; ?> -->
<!-- Glassmorphism & Professional Styles -->
<style>
.agent-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    transition: transform 0.4s ease, box-shadow 0.4s ease;
    cursor: pointer;
}
.agent-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.25);
}
.agent-img-wrapper {
    overflow: hidden;
    border-bottom-left-radius: 1rem;
    border-bottom-right-radius: 1rem;
}
.agent-img {
    width: 100%;
    height: 280px;
    object-fit: cover;
    transition: transform 0.4s ease;
}
.agent-card:hover .agent-img {
    transform: scale(1.05);
}
.agent-info h5 {
    color: #0d6efd;
    font-family: 'Poppins', sans-serif;
}
.agent-info p {
    color: #333;
    font-size: 0.95rem;
}
.agent-info i {
    color: #7010eeff;
}
.agent-info .btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    font-size: 1.1rem;
    transition: transform 0.3s;
}
.agent-info .btn:hover {
    transform: scale(1.1);
}
</style>
