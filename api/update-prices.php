<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Symbol.php';
require_once __DIR__ . '/../classes/Investment.php';
require_once __DIR__ . '/../middleware/Auth.php';

checkAuth();
header('Content-Type: application/json');

try {
    // Load configuration and initialize dependencies
    $config = require __DIR__ . '/../config/config.php';
    $db = new Database($config['database']);
    $symbol = new Symbol($db);
    $investment = new Investment($db, $symbol);

    // Get current timestamp before updates
    $startTime = microtime(true);


    // Fetch all open investments for the authenticated user
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        throw new Exception("User ID is missing in the session.");
    }

    // Fetch all open investments for the authenticated user
    $investments = $investment->getAllOpenInvestments($userId, true);

    // Calculate totals
    $totalInvestment = 0;
    $totalValue = 0;
    $totalProfitLoss = 0;

    foreach ($investments as &$inv) {
        $totalInvestment += ($inv['buy_price'] * $inv['amount']);
        if ($inv['current_price']) {
            $totalValue += ($inv['current_price'] * $inv['amount']);
            $totalProfitLoss += $inv['profit_loss'];
        }
        // Format numbers for display
        $inv['formatted_profit_loss'] = number_format($inv['profit_loss'], 3);
        $inv['formatted_current_price'] = number_format($inv['current_price'], 4);
    }

    // Calculate profit/loss percentage
    $profitLossPercentage = $totalInvestment > 0 ?
        ($totalProfitLoss / $totalInvestment) * 100 : 0;

    // Calculate execution time
    $executionTime = round((microtime(true) - $startTime) * 1000, 2);

    echo json_encode([
        'success' => true,
        'data' => [
            'investments' => $investments,
            'summary' => [
                'total_investment' => $totalInvestment,
                'total_value' => $totalValue,
                'total_profit_loss' => $totalProfitLoss,
                'profit_loss_percentage' => $profitLossPercentage
            ]
        ],
        'timestamp' => date('Y-m-d H:i:s'),
        'execution_time_ms' => $executionTime
    ]);
} catch (Exception $e) {
    error_log("[API] Price update error: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to update prices',
        'message' => $e->getMessage()
    ]);
}