<?php
class Auth {
    private $conn;
    public function __construct($db) {
        $this->conn = $db;
    }

    // REGISTER USER
    public function register($data, $files) {
        $name = trim($data['name']);
        $email = trim($data['email']);
        $password = $data['password'];
        $confirm = $data['confirm_password'];
        $role = $data['role'];
        $trade_license = $data['trade_license'] ?? null;

        // Validate fields
        if(!$name || !$email || !$password || !$confirm || !$role) {
            throw new Exception("All fields are required.");
        }
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }
        if($password !== $confirm) {
            throw new Exception("Passwords do not match.");
        }

        // Check existing email
        $table = $role==='buyer'?'clients':'agents';
        $stmt = $this->conn->prepare("SELECT id FROM $table WHERE email=? LIMIT 1");
        $stmt->execute([$email]);
        if($stmt->rowCount() > 0) throw new Exception("Email already exists.");

        // Upload seller document
        $documentPath = null;
        if($role==='seller' && isset($files['document']) && $files['document']['error']===0) {
            $allowed = ['pdf','jpg','jpeg'];
            $ext = strtolower(pathinfo($files['document']['name'], PATHINFO_EXTENSION));
            if(!in_array($ext, $allowed)) throw new Exception("Invalid file type.");
            if($files['document']['size']>5*1024*1024) throw new Exception("File too large.");
            $documentPath = "uploads/docs/".uniqid().".".$ext;
            move_uploaded_file($files['document']['tmp_name'], $documentPath);
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        if($role==='buyer') {
            $stmt = $this->conn->prepare("INSERT INTO clients (name,email,password,created_at) VALUES (?,?,?,NOW())");
            $stmt->execute([$name,$email,$hashed]);
        } else {
            $stmt = $this->conn->prepare("INSERT INTO agents (name,email,password,trade_license,document_path,created_at) VALUES (?,?,?,?,?,NOW())");
            $stmt->execute([$name,$email,$hashed,$trade_license,$documentPath]);
        }
        return true;
    }

    // LOGIN USER
    public function login($email, $password) {
        // Check clients first
        $stmt = $this->conn->prepare("SELECT * FROM clients WHERE email=? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $role = 'buyer';

        if(!$user) {
            $stmt = $this->conn->prepare("SELECT * FROM agents WHERE email=? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $role = 'seller';
        }

        if($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $role;
            return $role;
        } else {
            throw new Exception("Invalid email or password.");
        }
    }
}
