<?php
// controllers/analytics.php
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Analytics.php';
require_once __DIR__ . '/../classes/Symbol.php';
require_once __DIR__ . '/../classes/Investment.php';

checkAuth();

try {
    $config = require __DIR__ . '/../config/config.php';
    $db = new Database($config['database']);
    $analytics = new Analytics($db);

    // Debug: Check if we're getting user ID
    $userId = $_SESSION['user_id'];
    error_log("Processing analytics for user ID: " . $userId);

    // Get analytics data with debug logging
    try {
        $portfolioHistory = $analytics->getPortfolioHistory($userId);
        error_log("Portfolio History count: " . count($portfolioHistory));
    } catch (Exception $e) {
        error_log("Portfolio History Error: " . $e->getMessage());
        $portfolioHistory = [];
    }

    try {
        $distribution = $analytics->getInvestmentDistribution($userId);
        error_log("Distribution data count: " . count($distribution));
    } catch (Exception $e) {
        error_log("Distribution Error: " . $e->getMessage());
        $distribution = [];
    }

    try {
        $monthlyPerformance = $analytics->getMonthlyPerformance($userId);
        error_log("Monthly Performance count: " . count($monthlyPerformance));
    } catch (Exception $e) {
        error_log("Monthly Performance Error: " . $e->getMessage());
        $monthlyPerformance = [];
    }

    try {
        $winLossRatio = $analytics->getWinLossRatio($userId);
        error_log("Win/Loss Ratio: " . json_encode($winLossRatio));
    } catch (Exception $e) {
        error_log("Win/Loss Ratio Error: " . $e->getMessage());
        $winLossRatio = ['wins' => 0, 'losses' => 0];
    }

    try {
        $tradeMetrics = $analytics->getTradeMetrics($userId);
        error_log("Trade Metrics: " . json_encode($tradeMetrics));
    } catch (Exception $e) {
        error_log("Trade Metrics Error: " . $e->getMessage());
        $tradeMetrics = [];
    }

    // Extract metrics with default values
    $stats = [
        'averageProfitPerTrade' => $tradeMetrics['averageProfitPerTrade'] ?? 0,
        'averageLossPerTrade' => $tradeMetrics['averageLossPerTrade'] ?? 0,
        'profitFactor' => $tradeMetrics['profitFactor'] ?? 0,
        'avgHoldTime' => $tradeMetrics['avgHoldTime'] ?? 0,
        'bestMonthProfit' => $tradeMetrics['bestMonthProfit'] ?? 0,
        'worstMonthLoss' => $tradeMetrics['worstMonthLoss'] ?? 0,
        'largestWin' => $tradeMetrics['largestWin'] ?? 0,
        'largestLoss' => $tradeMetrics['largestLoss'] ?? 0,
        'riskRewardRatio' => $tradeMetrics['riskRewardRatio'] ?? 0,
        'totalTrades' => $tradeMetrics['totalTrades'] ?? 0,
        'winningTrades' => $tradeMetrics['winningTrades'] ?? 0,
        'losingTrades' => $tradeMetrics['losingTrades'] ?? 0,
        'successRate' => $tradeMetrics['successRate'] ?? 0,
        'monthlyConsistency' => $tradeMetrics['monthlyConsistency'] ?? 0,
        'profitableMonths' => $tradeMetrics['profitableMonths'] ?? 0,
        'totalMonths' => $tradeMetrics['totalMonths'] ?? 0
    ];

    // Extract variables for view
    extract($stats);

    // Format chart data
    $chartData = [
        'portfolioHistory' => [
            'labels' => array_column($portfolioHistory, 'date'),
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
            'labels' => array_column($monthlyPerformance, 'month'),
            'values' => array_column($monthlyPerformance, 'profit_loss')
        ]
    ];

} catch(Exception $e) {
    error_log("Main Analytics Error: " . $e->getMessage());
    $error = "Error fetching analytics data";

    // Initialize empty data
    $chartData = [
        'portfolioHistory' => ['labels' => [], 'values' => ['invested' => [], 'current' => []]],
        'distribution' => ['labels' => [], 'values' => []],
        'monthlyPerformance' => ['labels' => [], 'values' => []]
    ];

    // Initialize default stats
    $stats = [
        'averageProfitPerTrade' => 0,
        'averageLossPerTrade' => 0,
        'profitFactor' => 0,
        'avgHoldTime' => 0,
        'bestMonthProfit' => 0,
        'worstMonthLoss' => 0,
        'largestWin' => 0,
        'largestLoss' => 0,
        'riskRewardRatio' => 0,
        'totalTrades' => 0,
        'winningTrades' => 0,
        'losingTrades' => 0,
        'successRate' => 0,
        'monthlyConsistency' => 0,
        'profitableMonths' => 0,
        'totalMonths' => 0
    ];

    extract($stats);
}

require 'views/analytics.view.php';