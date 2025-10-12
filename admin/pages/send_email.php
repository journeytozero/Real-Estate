<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // PHPMailer path

header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['status'=>'error', 'message'=>'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = $_POST['to'] ?? '';
    $message = $_POST['message'] ?? '';
    $subject = $_POST['subject'] ?? 'No Subject';

    if(!$to || !$message){
        echo json_encode(['status'=>'error', 'message'=>'Recipient and message required']);
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your_gmail@gmail.com';
        $mail->Password = 'your_app_password';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('your_gmail@gmail.com', 'Admin');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = nl2br(htmlspecialchars($message));

        $mail->send();
        echo json_encode(['status'=>'ok', 'message'=>'Email sent']);
    } catch (Exception $e) {
        echo json_encode(['status'=>'error', 'message'=>$mail->ErrorInfo]);
    }
}
