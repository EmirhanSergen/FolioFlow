<?php
// controllers/dashboard.php
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Dashboard.php';
require_once __DIR__ . '/../classes/Symbol.php';
require_once __DIR__ . '/../classes/Investment.php';

checkAuth();

try {
    $config = require __DIR__ . '/../config/config.php';
    $db = new Database($config['database']);
    $dashboard = new Dashboard($db);
    $symbol = new Symbol($db);
    $investment = new Investment($db, $symbol);

    // Fetch core dashboard metrics
    $activeInvestmentCount = $dashboard->getActiveInvestmentCount($_SESSION['user_id']);
    $investmentData = $dashboard->calculateTotalInvestmentByUserId($_SESSION['user_id']);
    $totalProfitLoss = $dashboard->calculateTotalProfitByUserId($_SESSION['user_id']);
    $roi = $dashboard->calculateROI($_SESSION['user_id']);
    $performance = $dashboard->getPerformanceExtremes($_SESSION['user_id']);

    // Prepare variables for view
    $totalInitialInvestment = $investmentData['total_investment'] ?? 0;
    $totalValue = $investmentData['current_value'] ?? 0;
    $bestPerforming = $performance['best'];
    $worstPerforming = $performance['worst'];

    // Calculate performance metrics
    $bestReturn = $bestPerforming ? (($bestPerforming['current_price'] - $bestPerforming['buy_price']) / $bestPerforming['buy_price'] * 100) : 0;
    $worstReturn = $worstPerforming ? (($worstPerforming['current_price'] - $worstPerforming['buy_price']) / $worstPerforming['buy_price'] * 100) : 0;

} catch(Exception $e) {
    error_log("Dashboard Error: " . $e->getMessage());
    $error = "Error fetching dashboard data";
}

require 'views/dashboard.view.php';