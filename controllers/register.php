<?php
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Registration.php';

checkGuest(); // Only allow non-logged-in users

$config = require __DIR__ . '/../config/config.php';
$db = new Database($config['database']);
$registration = new Registration($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $registration->handleRegistration($_POST);

    if (isset($result['success'])) {
        header('Location: /FolioFlow/login');
        exit();
    }

    $errors = $result;
}

require 'views/register.view.php';