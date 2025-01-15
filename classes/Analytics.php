<?php
class Analytics {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function getPortfolioHistoryByPeriod($userId, $period = 'daily') {
        $groupBy = '';

        // Determine the GROUP BY clause based on the selected period
        switch ($period) {
            case 'daily':
                $groupBy = "DATE_FORMAT(i.created_at, '%Y-%m-%d')";
                break;
            case 'weekly':
                $groupBy = "YEAR(i.created_at), WEEK(i.created_at)";
                break;
            case 'monthly':
                $groupBy = "DATE_FORMAT(i.created_at, '%Y-%m')";
                break;
            default:
                throw new Exception("Unsupported period: $period");
        }

        // SQL query with dynamic grouping
        $stmt = $this->db->prepare("
        SELECT 
            $groupBy as period,
            SUM(i.buy_price * i.amount) as invested_value,
            SUM(
                CASE 
                    WHEN i.status = 'closed' THEN i.sell_price * i.amount
                    WHEN s.current_price IS NOT NULL THEN s.current_price * i.amount
                    ELSE i.buy_price * i.amount
                END
            ) as current_value
            FROM investments i
            LEFT JOIN symbols s ON i.name = s.symbol
            WHERE i.user_id = ?
            GROUP BY period
            ORDER BY period
        ");

        // Execute the query with the user ID
        $stmt->execute([$userId]);

        // Fetch and return the results
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function getInvestmentDistribution($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    i.name,
                    SUM(i.buy_price * i.amount) as total_invested,
                    SUM(
                        CASE 
                            WHEN s.current_price IS NOT NULL THEN s.current_price * i.amount
                            ELSE i.buy_price * i.amount
                        END
                    ) as current_value
                FROM investments i
                LEFT JOIN symbols s ON i.name = s.symbol
                WHERE i.user_id = ? AND i.status = 'active'
                GROUP BY i.name
                ORDER BY current_value DESC
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error getting investment distribution: " . $e->getMessage());
            throw new Exception("Failed to get investment distribution");
        }
    }

    public function getPerformanceByPeriod($userId, $period = 'monthly') {
        $groupBy = '';

        switch ($period) {
            case 'daily':
                $groupBy = 'DATE(i.created_at)';
                break;
            case 'weekly':
                $groupBy = 'YEAR(i.created_at), WEEK(i.created_at)';
                break;
            case 'monthly':
                $groupBy = 'DATE_FORMAT(i.created_at, \'%Y-%m\')';
                break;
            default:
                throw new Exception("Unsupported period: $period");
        }

        $stmt = $this->db->prepare("
        SELECT 
            $groupBy as period,
            COUNT(*) as total_trades,
            SUM(
                CASE 
                    WHEN i.status = 'closed' THEN (i.sell_price - i.buy_price) * i.amount
                    WHEN s.current_price IS NOT NULL THEN (s.current_price - i.buy_price) * i.amount
                    ELSE 0
                END
            ) as profit_loss
        FROM investments i
        LEFT JOIN symbols s ON i.name = s.symbol
        WHERE i.user_id = ?
        GROUP BY period
        ORDER BY period
    ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }


    public function getWinLossRatio($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT
                    SUM(CASE WHEN (sell_price - buy_price)  > 0 THEN 1 ELSE 0 END) as wins,
                    SUM(CASE WHEN (sell_price - buy_price)  < 0 THEN 1 ELSE 0 END) as losses
                FROM investments
                WHERE user_id = ? AND status = 'closed'
            ");
            $stmt->execute([$userId]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Error getting win/loss ratio: " . $e->getMessage());
            throw new Exception("Failed to get win/loss ratio");
        }
    }

    public function getTradeMetrics($userId) {
        try {
            // First get overall statistics
            $statsStmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_trades,
                SUM(CASE WHEN (sell_price - buy_price) > 0 THEN 1 ELSE 0 END) as winning_trades,
                SUM(CASE WHEN (sell_price - buy_price)  < 0 THEN 1 ELSE 0 END) as losing_trades,
                AVG(CASE WHEN (sell_price - buy_price)  > 0 
                    THEN (sell_price - buy_price) * amount ELSE NULL END) as avg_profit,
                AVG(CASE WHEN (sell_price - buy_price)  < 0 
                    THEN ABS((sell_price - buy_price) * amount) ELSE NULL END) as avg_loss,
                MAX((sell_price - buy_price) * amount) as largest_win,
                ABS(MIN((sell_price - buy_price) * amount)) as largest_loss,
                SUM(CASE WHEN (sell_price - buy_price) > 0 
                    THEN (sell_price - buy_price) * amount ELSE 0 END) as total_gains,
                ABS(SUM(CASE WHEN (sell_price - buy_price)  < 0 
                    THEN (sell_price - buy_price) * amount ELSE 0 END)) as total_losses
            FROM investments 
            WHERE user_id = ? AND status = 'closed'
        ");
            $statsStmt->execute([$userId]);
            $stats = $statsStmt->fetch();

            // Get monthly profits
            $monthlyStmt = $this->db->prepare("
            SELECT 
                DATE_FORMAT(closed_at, '%Y-%m') as month,
                SUM((sell_price - buy_price) * amount) as monthly_profit
            FROM investments
            WHERE user_id = ? AND status = 'closed'
            GROUP BY DATE_FORMAT(closed_at, '%Y-%m')
            ORDER BY monthly_profit DESC
        ");
            $monthlyStmt->execute([$userId]);
            $monthlyResults = $monthlyStmt->fetchAll();

            // Calculate derived metrics safely
            $totalTrades = $stats['total_trades'] ?? 0;
            $winningTrades = $stats['winning_trades'] ?? 0;
            $losingTrades = $stats['losing_trades'] ?? 0;
            $successRate = $totalTrades > 0 ? ($winningTrades / $totalTrades) * 100 : 0;
            $profitFactor = $stats['total_losses'] > 0 ? $stats['total_gains'] / $stats['total_losses'] : 0;

            // Process monthly metrics
            $monthlyProfits = array_column($monthlyResults, 'monthly_profit');
            $bestMonthProfit = !empty($monthlyProfits) ? max($monthlyProfits) : 0;
            $worstMonthLoss = !empty($monthlyProfits) ? min($monthlyProfits) : 0;

            $profitableMonths = count(array_filter($monthlyProfits, function($profit) {
                return $profit > 0;
            }));
            $totalMonths = count($monthlyProfits);
            $monthlyConsistency = $totalMonths > 0 ? ($profitableMonths / $totalMonths) * 100 : 0;

            return [
                'totalTrades' => $totalTrades,
                'winningTrades' => $winningTrades,
                'losingTrades' => $losingTrades,
                'successRate' => $successRate,
                'averageProfitPerTrade' => $stats['avg_profit'] ?? 0,
                'averageLossPerTrade' => $stats['avg_loss'] ?? 0,
                'profitFactor' => $profitFactor,
                'largestWin' => $stats['largest_win'] ?? 0,
                'largestLoss' => abs($stats['largest_loss'] ?? 0),
                'bestMonthProfit' => $bestMonthProfit,
                'worstMonthLoss' => $worstMonthLoss,
                'monthlyConsistency' => $monthlyConsistency,
                'profitableMonths' => $profitableMonths,
                'totalMonths' => $totalMonths,
                'avgHoldTime' => $this->calculateAverageHoldTime($userId)
            ];
        } catch (Exception $e) {
            error_log("Error calculating trade metrics: " . $e->getMessage());
            throw new Exception("Failed to calculate trade metrics");
        }
    }

    private function calculateAverageHoldTime($userId) {
        try {
            $stmt = $this->db->prepare("
            SELECT AVG(DATEDIFF(closed_at, created_at)) as avg_hold_time
            FROM investments
            WHERE user_id = ? AND status = 'closed'
        ");
            $stmt->execute([$userId]);
            $result = $stmt->fetch();
            return round($result['avg_hold_time'] ?? 0);
        } catch (Exception $e) {
            error_log("Error calculating average hold time: " . $e->getMessage());
            return 0;
        }
    }

    public function getTradingPatterns($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    DAYNAME(created_at) as day_of_week,
                    COUNT(*) as trade_count,
                    AVG((CASE 
                        WHEN status = 'closed' THEN (sell_price - buy_price) * amount
                        WHEN current_price IS NOT NULL THEN (current_price - buy_price) * amount
                        ELSE 0 
                    END)) as avg_profit
                FROM investments i
                LEFT JOIN symbols s ON i.name = s.symbol
                WHERE user_id = ?
                GROUP BY DAYNAME(created_at)
                ORDER BY FIELD(DAYNAME(created_at), 
                    'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error analyzing trading patterns: " . $e->getMessage());
            throw new Exception("Failed to analyze trading patterns");
        }
    }
}