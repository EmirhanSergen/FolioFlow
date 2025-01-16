<?php
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Registration.php';

checkGuest(); // Only allow non-logged-in users

$config = require __DIR__ . '/../config/config.php';
$db = new Database($config['database']);
$registration = new Registration($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        $result = $registration->handleRegistration($_POST);
    } catch (Exception $e) {
        $errors['general'] = "An unexpected error occurred. Please try again.";
        error_log($e->getMessage());
    }

    if (isset($result['success'])) {
        header('Location: /FolioFlow/login');
        exit();
    }

    $errors = $result;
}

require 'views/register.view.php';