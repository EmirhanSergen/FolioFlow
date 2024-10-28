<?php
// controllers/dashboard.php
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Dashboard.php';

checkAuth();

$config = require __DIR__ . '/../config/config.php';
$db = new Database($config['database']);
$dashboard = new Dashboard($db);

try {
    $investmentCount = $dashboard->getInvestmentCount($_SESSION['user_id']);
    // Get other dashboard data using dashboard class methods...
    $totalValue = $dashboard->calculateTotalInvestmentByUserId($_SESSION['user_id']);
    $totalProfitLoss = $dashboard->calculateProfitByUserId($_SESSION['user_id']);

} catch(Exception $e) {
    $error = "Error fetching dashboard data";
    error_log($e->getMessage());
}

require 'views/dashboard.view.php';