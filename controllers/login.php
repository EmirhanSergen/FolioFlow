<?php
// controllers/login.php
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../classes/Database.php';

checkGuest(); // Only allow non-logged-in users

$config = require __DIR__ . '/../config/config.php';
$db = new Database($config['database']);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate input
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required';
    }

    if (empty($errors)) {
        try {
            // Check user credentials
            $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['username'] = $user['username'];

                header('Location: /FolioFlow/dashboard');
                exit();
            } else {
                $errors['general'] = 'Invalid email or password';
            }

        } catch(PDOException $e) {
            $errors['general'] = "Login failed. Please try again.";
            error_log($e->getMessage());
        }
    }
}

require 'views/login.view.php';