<?php
// controllers/analytics.php

// Include necessary classes and middleware
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Analytics.php';

// Ensure the user is authenticated
checkAuth();

try {
    // Load configuration and initialize database connection
    $config = require __DIR__ . '/../config/config.php';
    $db = new Database($config['database']);

    // Check if the Database class provides a PDO instance
    if (!isset($db->connection) || !$db->connection instanceof PDO) {
        throw new Exception("Database connection not established.");
    }

    // Initialize Analytics class with the PDO connection
    $analytics = new Analytics($db->connection);

    // Get the current user's ID from the session
    $userId = $_SESSION['user_id'];

    /**
     * 1. Fetch Overall Portfolio
     */
    $overallPortfolio = $analytics->getOverallPortfolio($userId);
    $totalInvested = $overallPortfolio['totalInvested'];
    $currentValue = $overallPortfolio['currentValue'];

    /**
     * 2. Fetch Investment Distribution
     */
    $investmentDistributionData = $analytics->getInvestmentDistribution($userId);

    // Structure data for the Investment Distribution Chart
    $chartData['distribution']['labels'] = array_column($investmentDistributionData, 'name');
    $chartData['distribution']['values'] = array_map(function($item) {
        return round($item['currentValue'], 2);
    }, $investmentDistributionData);

    /**
     * 3. Fetch Overall Performance
     */
    $overallPerformance = $analytics->getOverallPerformance($userId);
    $totalProfit = $currentValue-$totalInvested ;
    // For the Portfolio Chart, represent overall invested vs current value
    $chartData['portfolioChart']['labels'] = ['Invested','Overall Profit', 'Current'];
    $chartData['portfolioChart']['values'] = [$totalInvested, $totalProfit , $currentValue];

    /**
     * 4. Fetch Win/Loss Ratio
     */
    $winLossRatio = $analytics->getWinLossRatio($userId);

    /**
     * 5. Fetch Trade Metrics
     */
    $tradeMetrics = $analytics->getTradeMetrics($userId);

    // Assign trade metrics to individual variables
    $averageProfitPerTrade = round($tradeMetrics['averageProfitPerTrade'] ?? 0, 2);
    $averageLossPerTrade = round($tradeMetrics['averageLossPerTrade'] ?? 0, 2);
    $profitFactor = round($tradeMetrics['profitFactor'] ?? 0, 2);
    $avgHoldTime = round($tradeMetrics['avgHoldTime'] ?? 0, 2);
    $bestMonthProfit = round($tradeMetrics['bestMonthProfit'] ?? 0, 2);
    $worstMonthLoss = round($tradeMetrics['worstMonthLoss'] ?? 0, 2);
    $largestWin = round($tradeMetrics['largestWin'] ?? 0, 2);
    $largestLoss = round($tradeMetrics['largestLoss'] ?? 0, 2);
    $riskRewardRatio = round($tradeMetrics['riskRewardRatio'] ?? 0, 2);

    /**
     * 6. Fetch Monthly Profits for Chart
     */
    $monthlyProfitsData = $analytics->fetchMonthlyProfits($userId);
    $chartData['monthlyPerformance']['labels'] = array_column($monthlyProfitsData, 'month');
    $chartData['monthlyPerformance']['values'] = array_map(function($item) {
        return round($item['monthlyProfit'], 2);
    }, $monthlyProfitsData);

} catch(Exception $e) {
    // Log any errors and set an error message for the view
    error_log("Analytics Error: " . $e->getMessage());
    $error = "Error fetching analytics data";
}

// Include the analytics view
require __DIR__ . '/../views/analytics.view.php';
?>
