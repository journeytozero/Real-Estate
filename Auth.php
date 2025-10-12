<?php
class Auth
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * REGISTER USER
     */
    public function register($data, $files = [])
    {
        $name     = trim($data['name'] ?? '');
        $email    = strtolower(trim($data['email'] ?? '')); // normalize email
        $password = $data['password'] ?? '';
        $confirm  = $data['confirm_password'] ?? '';
        $role     = $data['role'] ?? '';

        // Validate required fields
        if (!$name || !$email || !$password || !$confirm || !$role) {
            return ['status' => false, 'message' => 'All fields are required.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['status' => false, 'message' => 'Invalid email format.'];
        }

        if ($password !== $confirm) {
            return ['status' => false, 'message' => 'Passwords do not match.'];
        }

        if (!in_array($role, ['buyer', 'agent'])) {
            return ['status' => false, 'message' => 'Invalid role selected.'];
        }

        // Check if email exists (case-insensitive)
        $table = $role === 'buyer' ? 'clients' : 'agents';
        $stmt = $this->conn->prepare("SELECT id FROM $table WHERE LOWER(TRIM(email)) = ? LIMIT 1");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['status' => false, 'message' => 'Email already exists.'];
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $trade_license_path = null;

        // Handle agent trade license
        if ($role === 'agent') {
            if (empty($files['trade_license']['name'] ?? '')) {
                return ['status' => false, 'message' => 'Trade license is required for agents.'];
            }

            $allowed_types = ['application/pdf', 'image/jpeg', 'image/png'];
            if (!in_array($files['trade_license']['type'], $allowed_types)) {
                return ['status' => false, 'message' => 'Invalid trade license type. Allowed: PDF, JPG, PNG.'];
            }

            $upload_dir = __DIR__ . '/../uploads/docs/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            $filename = time() . '_' . preg_replace("/[^a-zA-Z0-9\._-]/", "", basename($files['trade_license']['name']));
            $destination = $upload_dir . $filename;

            if (!move_uploaded_file($files['trade_license']['tmp_name'], $destination)) {
                return ['status' => false, 'message' => 'Failed to upload trade license.'];
            }

            $trade_license_path = 'uploads/docs/' . $filename;
        }

        // Insert user into database
        try {
            if ($role === 'buyer') {
                // Assign first agent ID or NULL if none exists
                $stmt = $this->conn->query("SELECT id FROM agents ORDER BY id ASC LIMIT 1");
                $agent = $stmt->fetch(PDO::FETCH_ASSOC);
                $agent_id = $agent['id'] ?? null;

                $stmt = $this->conn->prepare("
                    INSERT INTO clients (agent_id, name, email, password, created_at)
                    VALUES (?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$agent_id, $name, $email, $hashed]);
            } else {
                $stmt = $this->conn->prepare("
                    INSERT INTO agents (name, email, password, trade_license, created_at)
                    VALUES (?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$name, $email, $hashed, $trade_license_path]);
            }

            return ['status' => true, 'message' => 'Registration successful!'];
        } catch (PDOException $e) {
            return ['status' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    /**
     * LOGIN USER
     */
    public function login($email, $password)
    {
        $email = strtolower(trim($email)); // normalize email
        $roles = [
            'admin' => 'admins',
            'agent' => 'agents',
            'buyer' => 'clients'
        ];

        foreach ($roles as $role => $table) {
            $stmt = $this->conn->prepare("SELECT * FROM $table WHERE LOWER(TRIM(email)) = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);

                switch ($role) {
                    case 'admin':
                        $_SESSION['admin_id'] = $user['id'];
                        $_SESSION['admin_name'] = $user['name'];
                        $_SESSION['admin_email'] = $user['email'];
                        break;
                    case 'agent':
                        $_SESSION['agent_id'] = $user['id'];
                        $_SESSION['agent_name'] = $user['name'];
                        $_SESSION['agent_email'] = $user['email'];
                        break;
                    case 'buyer':
                        $_SESSION['client_id'] = $user['id'];
                        $_SESSION['client_name'] = $user['name'];
                        $_SESSION['client_email'] = $user['email'];
                        break;
                }

                $_SESSION['user_role'] = $role;
                return ['status' => true, 'role' => $role];
            }
        }

        return ['status' => false, 'message' => 'Invalid email or password.'];
    }

    /**
     * LOGOUT USER
     */
    public function logout($role = null)
    {
        switch ($role) {
            case 'admin':
                unset($_SESSION['admin_id'], $_SESSION['admin_name'], $_SESSION['admin_email']);
                break;
            case 'agent':
                unset($_SESSION['agent_id'], $_SESSION['agent_name'], $_SESSION['agent_email']);
                break;
            case 'buyer':
                unset($_SESSION['client_id'], $_SESSION['client_name'], $_SESSION['client_email']);
                break;
        }

        unset($_SESSION['user_role']);
        if (empty($_SESSION)) session_destroy();

        return ['status' => true, 'message' => 'Logged out successfully.'];
    }
}
