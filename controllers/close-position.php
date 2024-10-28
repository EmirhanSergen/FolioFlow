<?php
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Symbol.php';
require_once __DIR__ . '/../classes/Investment.php';

checkAuth();

try {
    // Get investment ID and price from URL
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $price = filter_input(INPUT_GET, 'price', FILTER_VALIDATE_FLOAT);

    if (!$id || $price === false) {
        $_SESSION['error_message'] = "Invalid investment ID or price";
        header('Location: /FolioFlow/investments');
        exit();
    }

    // Initialize database and investment
    $config = require __DIR__ . '/../config/config.php';
    $db = new Database($config['database']);
    $symbol = new Symbol($db);
    $investment = new Investment($db, $symbol);

    // Close the investment
    $result = $investment->closeInvestment($id, $price);

    if ($result) {
        $_SESSION['success_message'] = "Investment closed successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to close investment";
    }

} catch(Exception $e) {
    error_log("Error closing investment: " . $e->getMessage());
    $_SESSION['error_message'] = "An error occurred while closing the investment";
}

// Redirect back to investments page
header('Location: /FolioFlow/investments');
exit();