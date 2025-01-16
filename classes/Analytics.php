<?php
/**
 * Example Analytics class with improvements and minor docblocks.
 */
class Analytics
{
    /** @var PDO */
    private $db;

    /**
     * Constructor expects a PDO or Database wrapper
     *
     * @param PDO $database
     */
    public function __construct($database)
    {
        $this->db = $database;
    }

    /**
     * Get the portfolio history, grouped by day, week, or month.
     *
     * @param int    $userId
     * @param string $period
     * @return array
     * @throws Exception
     */
    public function getPortfolioHistoryByPeriod($userId, $period = 'daily')
    {
        // Determine the GROUP BY clause based on the selected period
        switch ($period) {
            case 'daily':
                $groupBy = 'DATE(created_at)';
                break;
            case 'weekly':
                $groupBy = 'YEAR(created_at), WEEK(created_at)';
                break;
            case 'monthly':
                $groupBy = 'YEAR(created_at), MONTH(created_at)';
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

        // Execute the query
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get the distribution of current investment by symbol.
     *
     * @param int $userId
     * @return array
     * @throws Exception
     */
    public function getInvestmentDistribution($userId)
    {
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

    /**
     * Get performance (profit/loss) grouped by day/week/month.
     *
     * @param int    $userId
     * @param string $period
     * @return array
     * @throws Exception
     */
    public function getPerformanceByPeriod($userId, $period = 'monthly')
    {
        switch ($period) {
            case 'daily':
                $groupBy = 'DATE(i.created_at)';
                break;
            case 'weekly':
                $groupBy = 'YEAR(i.created_at), WEEK(i.created_at)';
                break;
            case 'monthly':
                $groupBy = "DATE_FORMAT(i.created_at, '%Y-%m')";
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

    /**
     * Get the count of winning vs losing trades for a user.
     *
     * @param int $userId
     * @return array
     * @throws Exception
     */
    public function getWinLossRatio($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT
                    SUM(CASE WHEN (sell_price - buy_price) > 0 THEN 1 ELSE 0 END) as wins,
                    SUM(CASE WHEN (sell_price - buy_price) < 0 THEN 1 ELSE 0 END) as losses
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

    private function fetchOverallTradeStatistics($userId) {
        try {
            $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_trades,
                SUM(CASE WHEN (sell_price - buy_price) > 0 THEN 1 ELSE 0 END) as winning_trades,
                SUM(CASE WHEN (sell_price - buy_price) < 0 THEN 1 ELSE 0 END) as losing_trades,
                AVG(CASE WHEN (sell_price - buy_price) > 0 
                    THEN (sell_price - buy_price) * amount ELSE NULL END) as avg_profit,
                AVG(CASE WHEN (sell_price - buy_price) < 0 
                    THEN ABS((sell_price - buy_price) * amount) ELSE NULL END) as avg_loss,
                MAX((sell_price - buy_price) * amount) as largest_win,
                ABS(MIN((sell_price - buy_price) * amount)) as largest_loss,
                SUM(CASE WHEN (sell_price - buy_price) > 0 
                    THEN (sell_price - buy_price) * amount ELSE 0 END) as total_gains,
                ABS(SUM(CASE WHEN (sell_price - buy_price) < 0 
                    THEN (sell_price - buy_price) * amount ELSE 0 END)) as total_losses
            FROM investments 
            WHERE user_id = ? AND status = 'closed'
        ");
            $stmt->execute([$userId]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Error fetching overall trade statistics: " . $e->getMessage());
            return [];
        }
    }

    private function fetchMonthlyProfits($userId) {
        try {
            $stmt = $this->db->prepare("
            SELECT 
                DATE_FORMAT(closed_at, '%Y-%m') as month,
                SUM((sell_price - buy_price) * amount) as monthly_profit
            FROM investments
            WHERE user_id = ? AND status = 'closed'
            GROUP BY DATE_FORMAT(closed_at, '%Y-%m')
            ORDER BY monthly_profit DESC
        ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error fetching monthly profits: " . $e->getMessage());
            return [];
        }
    }

    private function calculateDerivedMetrics($stats) {
        $totalTrades = $stats['total_trades'] ?? 0;
        $winningTrades = $stats['winning_trades'] ?? 0;
        $totalGains = $stats['total_gains'] ?? 0;
        $totalLosses = $stats['total_losses'] ?? 0;

        $successRate = $totalTrades > 0 ? ($winningTrades / $totalTrades) * 100 : 0;
        $profitFactor = $totalLosses > 0 ? ($totalGains / $totalLosses) : 0;

        return [
            'successRate' => $successRate,
            'profitFactor' => $profitFactor
        ];
    }

    public function getTradeMetrics($userId) {
        try {
            // Fetch overall statistics
            $stats = $this->fetchOverallTradeStatistics($userId);

            // Fetch monthly profits
            $monthlyResults = $this->fetchMonthlyProfits($userId);
            $monthlyProfits = array_column($monthlyResults, 'monthly_profit');

            // Calculate derived metrics
            $derivedMetrics = $this->calculateDerivedMetrics($stats);

            // Process monthly metrics
            $bestMonthProfit = !empty($monthlyProfits) ? max($monthlyProfits) : 0;
            $worstMonthLoss = !empty($monthlyProfits) ? min($monthlyProfits) : 0;

            $profitableMonths = count(array_filter($monthlyProfits, fn($profit) => $profit > 0));
            $totalMonths = count($monthlyProfits);
            $monthlyConsistency = $totalMonths > 0 ? ($profitableMonths / $totalMonths) * 100 : 0;

            return array_merge($stats, $derivedMetrics, [
                'bestMonthProfit' => $bestMonthProfit,
                'worstMonthLoss' => $worstMonthLoss,
                'monthlyConsistency' => $monthlyConsistency,
                'profitableMonths' => $profitableMonths,
                'totalMonths' => $totalMonths,
                'avgHoldTime' => $this->calculateAverageHoldTime($userId),
            ]);
        } catch (Exception $e) {
            error_log("Error calculating trade metrics: " . $e->getMessage());
            throw new Exception("Failed to calculate trade metrics");
        }
    }


    /**
     * Calculate average hold time for closed trades in days.
     *
     * @param int $userId
     * @return float|int
     */
    private function calculateAverageHoldTime($userId)
    {
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
}
