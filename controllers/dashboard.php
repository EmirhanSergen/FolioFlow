<?php
// controllers/dashboard.php

// Include necessary classes and middleware
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Dashboard.php';
require_once __DIR__ . '/../classes/Symbol.php';
require_once __DIR__ . '/../classes/Investment.php';

// Ensure the user is authenticated
checkAuth();

try {
    // Load configuration and initialize database connection
    $config = require __DIR__ . '/../config/config.php';
    $db = new Database($config['database']);

    // Initialize classes
    $dashboard = new Dashboard($db);
    $symbol = new Symbol($db);
    $investment = new Investment($db, $symbol);

    // Fetch core dashboard metrics related to active investments
    $activeInvestmentCount = $dashboard->getActiveInvestmentCount($_SESSION['user_id']);
    $investmentData = $dashboard->calculateTotalInvestmentByUserId($_SESSION['user_id']);
    $activeProfitLoss = $dashboard->calculateTotalProfitByUserId($_SESSION['user_id']);
    $roi = $dashboard->calculateROI($_SESSION['user_id']);
    $performance = $dashboard->getPerformanceExtremes($_SESSION['user_id']);

    // Prepare variables for the view
    $totalInitialInvestment = $investmentData['total_investment'] ?? 0;
    $totalValue = $investmentData['current_value'] ?? 0;
    $bestPerforming = $performance['best'];
    $worstPerforming = $performance['worst'];

    // Calculate performance metrics for best and worst investments
    $bestReturn = $bestPerforming
        ? (($bestPerforming['current_price'] - $bestPerforming['buy_price']) / $bestPerforming['buy_price'] * 100)
        : 0;
    $worstReturn = $worstPerforming
        ? (($worstPerforming['current_price'] - $worstPerforming['buy_price']) / $worstPerforming['buy_price'] * 100)
        : 0;

    // Optionally, you can pass the ROI directly if it's already calculated in the Dashboard class
    // $roi = $dashboard->calculateROI($_SESSION['user_id']);

} catch(Exception $e) {
    // Log any errors and set an error message for the view
    error_log("Dashboard Error: " . $e->getMessage());
    $error = "Error fetching dashboard data";
}

// Include the dashboard view
require 'views/dashboard.view.php';
?>
