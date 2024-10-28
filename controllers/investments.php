<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Symbol.php';
require_once __DIR__ . '/../classes/Investment.php';
require_once __DIR__ . '/../middleware/Auth.php';

checkAuth();

$errors = [];
$success = '';

try {
    $config = require __DIR__ . '/../config/config.php';
    $db = new Database($config['database']);
    $symbol = new Symbol($db);
    $investment = new Investment($db, $symbol);

    // Test Binance API connection
    $apiTest = $symbol->testApiConnection();
    if (!$apiTest['success']) {
        $errors[] = "Warning: Price updates may be unavailable";
        error_log("[Investments] API connection test failed: " . ($apiTest['error'] ?? 'Unknown error'));
    }

    // Get investments with prices
    $investments = $investment->getAllOpenInvestments($_SESSION['user_id']);

    // Calculate totals
    $totalInvestment = 0;
    $totalValue = 0;
    $totalProfitLoss = 0;

    foreach ($investments as $inv) {
        $totalInvestment += ($inv['buy_price'] * $inv['amount']);
        if ($inv['current_price']) {
            $totalValue += ($inv['current_price'] * $inv['amount']);
            $totalProfitLoss += $inv['profit_loss'];
        }
    }

    // Add to template variables
    $pageData = [
        'total_investment' => $totalInvestment,
        'total_value' => $totalValue,
        'total_profit_loss' => $totalProfitLoss,
        'profit_loss_percentage' => $totalInvestment > 0 ?
            ($totalProfitLoss / $totalInvestment) * 100 : 0
    ];

} catch(Exception $e) {
    $errors[] = "Error fetching investments";
    error_log("[Investments] Error: " . $e->getMessage());
    $investments = [];
    $pageData = [
        'total_investment' => 0,
        'total_value' => 0,
        'total_profit_loss' => 0,
        'profit_loss_percentage' => 0
    ];
}

require 'views/investments.view.php';