<?php
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/ClosedPosition.php';

// Check authentication
checkAuth();

$positions = [];
$errors = [];

// Initialize database and closed position model
$config = require __DIR__ . '/../config/config.php';
$db = new Database($config['database']);
$closedPosition = new ClosedPosition($db);

// Get all closed positions
try {
    $positions = $closedPosition->getAllClosedPositions($_SESSION['user_id']);
} catch(Exception $e) {
    $errors[] = "Error fetching closed positions";
    error_log($e->getMessage());
}

// Load the view
require 'views/closed-positions.view.php';