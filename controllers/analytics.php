<?php
// controllers/analytics.php

require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Analytics.php';

// Make sure user is authenticated
checkAuth();

// Prepare environment
try {
    // 1. Create DB / Analytics instance
    $config = require __DIR__ . '/../config/config.php';
    $db = new Database($config['database']);  // This presumably returns a PDO
    $analytics = new Analytics($db);

    // 2. Parse JSON from the request body
    $requestBody = file_get_contents('php://input');
    $data = json_decode($requestBody, true);
    if (!is_array($data)) {
        $data = [];
    }

    // 3. Get user ID from session
    session_start();
    if (empty($_SESSION['user_id'])) {
        throw new Exception("No user ID in session.");
    }
    $userId = (int) $_SESSION['user_id'];

    // 4. Extract parameters
    $portfolioPeriod   = $data['portfolio_period']   ?? 'daily';
    $performancePeriod = $data['performance_period'] ?? 'monthly';

    // 5. Fetch data
    try {
        $portfolioHistory = $analytics->getPortfolioHistoryByPeriod($userId, $portfolioPeriod);
    } catch (Exception $e) {
        error_log("Portfolio History Error: " . $e->getMessage());
        $portfolioHistory = [];
    }

    try {
        $performance = $analytics->getPerformanceByPeriod($userId, $performancePeriod);
    } catch (Exception $e) {
        error_log("Performance Error: " . $e->getMessage());
        $performance = [];
    }

    try {
        $distribution = $analytics->getInvestmentDistribution($userId);
        if (!$distribution) {
            $distribution = [['name' => 'No Data', 'current_value' => 0]];
        }
    } catch (Exception $e) {
        error_log("Distribution Error: " . $e->getMessage());
        $distribution = [['name' => 'No Data', 'current_value' => 0]];
    }

    try {
        $winLossRatio = $analytics->getWinLossRatio($userId);
    } catch (Exception $e) {
        error_log("Win/Loss Ratio Error: " . $e->getMessage());
        $winLossRatio = ['wins' => 0, 'losses' => 0];
    }

    try {
        $tradeMetrics = $analytics->getTradeMetrics($userId);
    } catch (Exception $e) {
        error_log("Trade Metrics Error: " . $e->getMessage());
        $tradeMetrics = [];
    }

    // 6. Build chart data
    $chartData = [
        'portfolioHistory' => [
            'labels' => array_column($portfolioHistory, 'period'),
            'values' => [
                'invested' => array_map(fn($v) => round($v, 2), array_column($portfolioHistory, 'invested_value')),
                'current'  => array_map(fn($v) => round($v, 2), array_column($portfolioHistory, 'current_value')),
            ],
        ],
        'distribution' => [
            'labels' => array_column($distribution, 'name'),
            'values' => array_map(fn($v) => round($v, 2), array_column($distribution, 'current_value')),
        ],
        'monthlyPerformance' => [
            'labels' => array_column($performance, 'period'),
            'values' => array_map(fn($v) => round($v, 2), array_column($performance, 'profit_loss')),
        ],
    ];

    // 7. Prepare tradeMetrics safely
    //    If you got an empty array, fill with zeros to avoid notice errors
    $defaults = [
        'totalTrades' => 0, 'winningTrades' => 0, 'losingTrades' => 0, 'successRate' => 0,
        'averageProfitPerTrade' => 0, 'averageLossPerTrade' => 0, 'profitFactor' => 0,
        'largestWin' => 0, 'largestLoss' => 0, 'bestMonthProfit' => 0, 'worstMonthLoss' => 0,
        'monthlyConsistency' => 0, 'profitableMonths' => 0, 'totalMonths' => 0, 'avgHoldTime' => 0
    ];
    $tradeMetrics = array_merge($defaults, $tradeMetrics);
    // Round numeric fields
    foreach ($tradeMetrics as $key => $val) {
        if (is_numeric($val)) {
            $tradeMetrics[$key] = round($val, 2);
        }
    }

    // 8. Return JSON
    header('Content-Type: application/json');
    echo json_encode([
        'chartData'    => $chartData,
        'winLossRatio' => $winLossRatio,
        'tradeMetrics' => $tradeMetrics,
    ]);
    exit;

} catch (Exception $e) {
    // If anything goes wrong, return an error
    error_log("Main Analytics Error: " . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Failed to fetch analytics data.']);
    exit;
}
