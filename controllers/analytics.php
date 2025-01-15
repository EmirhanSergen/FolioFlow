<?php
// controllers/analytics.php
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Analytics.php';

checkAuth();

try {
    $config = require __DIR__ . '/../config/config.php';
    $db = new Database($config['database']);
    $analytics = new Analytics($db);

    // Retrieve user ID
    $userId = $_SESSION['user_id'];


    $portfolioPeriod = $_POST['portfolio_period'] ?? 'daily';
    $performancePeriod = $_POST['performance_period'] ?? 'monthly';

    // Fetch portfolio history
    try {
        $portfolioHistory = $analytics->getPortfolioHistoryByPeriod($userId, $portfolioPeriod);
    } catch (Exception $e) {
        error_log("Portfolio History Error: " . $e->getMessage());
        $portfolioHistory = [];
    }

    // Fetch other analytics data
    try {
        $performance = $analytics->getPerformanceByPeriod($userId, $performancePeriod);
    } catch (Exception $e) {
        error_log("Monthly Performance Error: " . $e->getMessage());
        $performance = [];
    }

    try {
        $distribution = $analytics->getInvestmentDistribution($userId);
    } catch (Exception $e) {
        error_log("Distribution Error: " . $e->getMessage());
        $distribution = [];
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

    // Extract variables for the view
    $stats = [
        'averageProfitPerTrade' => $tradeMetrics['averageProfitPerTrade'] ?? 0,
        'averageLossPerTrade' => $tradeMetrics['averageLossPerTrade'] ?? 0,
        'profitFactor' => $tradeMetrics['profitFactor'] ?? 0,
        'largestWin' => $tradeMetrics['largestWin'] ?? 0,
        'largestLoss' => $tradeMetrics['largestLoss'] ?? 0,
        'totalTrades' => $tradeMetrics['totalTrades'] ?? 0,
        'successRate' => $tradeMetrics['successRate'] ?? 0
    ];

    // Format chart data
    $chartData = [
        'portfolioHistory' => [
            'labels' => array_column($portfolioHistory, 'period'),
            'values' => [
                'invested' => array_column($portfolioHistory, 'invested_value'),
                'current' => array_column($portfolioHistory, 'current_value')
            ]
        ],
        'distribution' => [
            'labels' => array_column($distribution, 'name'),
            'values' => array_column($distribution, 'current_value')
        ],
        'monthlyPerformance' => [
            'labels' => array_column($monthlyPerformance, 'period'),
            'values' => array_column($monthlyPerformance, 'profit_loss')
        ]
    ];

    // Pass data to the view
} catch (Exception $e) {
    error_log("Main Analytics Error: " . $e->getMessage());
    $error = "Error fetching analytics data";

    // Initialize empty data
    $chartData = [
        'portfolioHistory' => ['labels' => [], 'values' => ['invested' => [], 'current' => []]],
        'distribution' => ['labels' => [], 'values' => []],
        'monthlyPerformance' => ['labels' => [], 'values' => []]
    ];
}

require 'views/analytics.view.php';
