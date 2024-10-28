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

    // Get basic metrics
    $investmentCount = $dashboard->getInvestmentCount($_SESSION['user_id']);
    $investments = $investment->getAllOpenInvestments($_SESSION['user_id']);

    // Initialize variables
    $totalInitialInvestment = 0;
    $totalCurrentValue = 0;
    $totalProfitLoss = 0;
    $bestPerforming = null;
    $bestReturn = -INF;
    $worstPerforming = null;
    $worstReturn = INF;
    $roi = 0;
    $dayChange = 0;

    // Calculate portfolio metrics
    foreach ($investments as $inv) {
        if (isset($inv['current_price'], $inv['buy_price']) && $inv['buy_price'] > 0) {
            $initialValue = $inv['buy_price'] * $inv['amount'];
            $currentValue = $inv['current_price'] * $inv['amount'];

            $totalInitialInvestment += $initialValue;
            $totalCurrentValue += $currentValue;

            // Calculate return percentage for this investment
            $return = (($inv['current_price'] - $inv['buy_price']) / $inv['buy_price']) * 100;

            // Track best and worst performing
            if ($return > $bestReturn) {
                $bestReturn = $return;
                $bestPerforming = $inv;
            }
            if ($return < $worstReturn) {
                $worstReturn = $return;
                $worstPerforming = $inv;
            }
        }
    }

    // Calculate total profit/loss
    $totalProfitLoss = $totalCurrentValue - $totalInitialInvestment;

    // Calculate ROI
    if ($totalInitialInvestment > 0) {
        $roi = ($totalProfitLoss / $totalInitialInvestment) * 100;
    }

    // Variables for view
    $totalValue = $totalCurrentValue;

} catch(Exception $e) {
    error_log("Dashboard Error: " . $e->getMessage());
    $error = "Error fetching dashboard data";
}

require 'views/dashboard.view.php';