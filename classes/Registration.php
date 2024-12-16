<?php
// classes/Registration.php
class Registration {
    private $db;
    private $errors = [];

    // Db connection
    public function __construct($database) {
        $this->db = $database;
    }

    public function handleRegistration($data) {
        // Sanitize inputs
        $username = htmlspecialchars(trim($data['username'] ?? ''), ENT_QUOTES, 'UTF-8');
        $email = filter_var(trim($data['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = $data['password'] ?? '';
        $confirm_password = $data['confirm_password'] ?? '';

        // Validate input
        $this->validateInput($username, $email, $password, $confirm_password);

        if (empty($this->errors)) {
            return $this->createUser($username, $email, $password);
        }

        return $this->errors;
    }

    private function validateInput($username, $email, $password, $confirm_password) {
        // Username validation
        if (empty($username) || strlen($username) < 3) {
            $this->errors['username'] = 'Username must be at least 3 characters';
        }

        // Email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'Invalid email format';
        }

        // Password validation
        if (strlen($password) < 5) {
            $this->errors['password'] = 'Password must be at least 5 characters';
        } else {
            // Check for uppercase
            if (!preg_match('/[A-Z]/', $password)) {
                $this->errors['password'] = 'Password must contain at least one uppercase letter';
            }
            // Check for lowercase
            if (!preg_match('/[a-z]/', $password)) {
                $this->errors['password'] = 'Password must contain at least one lowercase letter';
            }
            // Check for number
            if (!preg_match('/[0-9]/', $password)) {
                $this->errors['password'] = 'Password must contain at least one number';
            }
            // Check for special character
            if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
                $this->errors['password'] = 'Password must contain at least one special character';
            }
        }

        if ($password !== $confirm_password) {
            $this->errors['confirm_password'] = 'Passwords do not match';
        }
    }

    private function createUser($username, $email, $password) {
        try {
            // Check existing user
            $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);

            if ($stmt->rowCount() > 0) {
                return ['general' => 'Username or email already exists'];
            }

            // Create user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $this->db->prepare(
                "INSERT INTO users (username, email, password) VALUES (?, ?, ?)"
            );

            $stmt->execute([$username, $email, $hashedPassword]);

            return ['success' => true];

        } catch (PDOException $e) {
            error_log($e->getMessage());
            return ['general' => 'Registration failed. Please try again.'];
        }
    }
}