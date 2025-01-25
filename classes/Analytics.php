<?php
/**
 * Analytics class for fetching investment-related analytics.
 */
class Analytics
{
    /** @var PDO */
    private $db;

    /**
     * Constructor expects a PDO instance.
     *
     * @param PDO $database
     */
    public function __construct(PDO $database)
    {
        $this->db = $database;
    }

    /**
     * Get the overall portfolio invested and current values.
     *
     * @param int $userId
     * @return array
     * @throws Exception
     */
    public function getOverallPortfolio(int $userId): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    SUM(i.buy_price * i.amount) AS totalInvested,
                    SUM(
                        CASE 
                            WHEN i.status = 'closed' THEN i.sell_price * i.amount
                            WHEN s.current_price IS NOT NULL THEN s.current_price * i.amount
                            ELSE i.buy_price * i.amount
                        END
                    ) AS currentValue
                FROM investments i
                LEFT JOIN symbols s ON i.name = s.symbol
                WHERE i.user_id = :userId
            ");
            $stmt->execute([':userId' => $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return [
                'totalInvested' => round($result['totalInvested'] ?? 0, 2),
                'currentValue'  => round($result['currentValue'] ?? 0, 2)
            ];
        } catch (Exception $e) {
            error_log("Error getting overall portfolio: " . $e->getMessage());
            throw new Exception("Failed to get overall portfolio");
        }
    }

    /**
     * Get the distribution of current investments by symbol.
     *
     * @param int $userId
     * @return array
     * @throws Exception
     */
    public function getInvestmentDistribution(int $userId): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    i.name,
                    SUM(i.buy_price * i.amount) AS totalInvested,
                    SUM(
                        CASE 
                            WHEN s.current_price IS NOT NULL THEN s.current_price * i.amount
                            ELSE i.buy_price * i.amount
                        END
                    ) AS currentValue
                FROM investments i
                LEFT JOIN symbols s ON i.name = s.symbol
                WHERE i.user_id = :userId AND i.status = 'active'
                GROUP BY i.name
                ORDER BY currentValue DESC
            ");
            $stmt->execute([':userId' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting investment distribution: " . $e->getMessage());
            throw new Exception("Failed to get investment distribution");
        }
    }

    /**
     * Get the overall performance (profit/loss).
     *
     * @param int $userId
     * @return float
     * @throws Exception
     */
    public function getOverallPerformance(int $userId): float
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    SUM(
                        CASE 
                            WHEN i.status = 'closed' THEN (i.sell_price - i.buy_price) * i.amount
                            WHEN s.current_price IS NOT NULL THEN (s.current_price - i.buy_price) * i.amount
                            ELSE 0
                        END
                    ) AS profitLoss
                FROM investments i
                LEFT JOIN symbols s ON i.name = s.symbol
                WHERE i.user_id = :userId
            ");
            $stmt->execute([':userId' => $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return round($result['profitLoss'] ?? 0, 2);
        } catch (Exception $e) {
            error_log("Error getting overall performance: " . $e->getMessage());
            throw new Exception("Failed to get overall performance");
        }
    }

    /**
     * Get the count of winning vs losing trades for a user.
     *
     * @param int $userId
     * @return array
     * @throws Exception
     */
    public function getWinLossRatio(int $userId): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT
                    SUM(CASE WHEN (sell_price - buy_price) > 0 THEN 1 ELSE 0 END) AS wins,
                    SUM(CASE WHEN (sell_price - buy_price) < 0 THEN 1 ELSE 0 END) AS losses
                FROM investments
                WHERE user_id = :userId AND status = 'closed'
            ");
            $stmt->execute([':userId' => $userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting win/loss ratio: " . $e->getMessage());
            throw new Exception("Failed to get win/loss ratio");
        }
    }

    /**
     * Fetch overall trade statistics for a user.
     *
     * @param int $userId
     * @return array
     */
    private function fetchOverallTradeStatistics(int $userId): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) AS totalTrades,
                    SUM(CASE WHEN (sell_price - buy_price) > 0 THEN 1 ELSE 0 END) AS winningTrades,
                    SUM(CASE WHEN (sell_price - buy_price) < 0 THEN 1 ELSE 0 END) AS losingTrades,
                    AVG(CASE WHEN (sell_price - buy_price) > 0 
                        THEN (sell_price - buy_price) * amount ELSE NULL END) AS averageProfitPerTrade,
                    AVG(CASE WHEN (sell_price - buy_price) < 0 
                        THEN ABS((sell_price - buy_price) * amount) ELSE NULL END) AS averageLossPerTrade,
                    MAX((sell_price - buy_price) * amount) AS largestWin,
                    ABS(MIN((sell_price - buy_price) * amount)) AS largestLoss,
                    SUM(CASE WHEN (sell_price - buy_price) > 0 
                        THEN (sell_price - buy_price) * amount ELSE 0 END) AS totalGains,
                    ABS(SUM(CASE WHEN (sell_price - buy_price) < 0 
                        THEN (sell_price - buy_price) * amount ELSE 0 END)) AS totalLosses
                FROM investments 
                WHERE user_id = :userId AND status = 'closed'
            ");
            $stmt->execute([':userId' => $userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching overall trade statistics: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetch monthly profits for a user.
     *
     * @param int $userId
     * @return array
     */
    public function fetchMonthlyProfits(int $userId): array
    {
        try {
            // Fetch unique months and profits for closed investments
            $stmt = $this->db->prepare("
            SELECT 
                DATE_FORMAT(closed_at, '%Y-%m') AS month,
                SUM((sell_price - buy_price) * amount) AS monthlyProfit
            FROM investments
            WHERE user_id = :userId AND status = 'closed'
            GROUP BY DATE_FORMAT(closed_at, '%Y-%m')
            ORDER BY closed_at ASC
        ");
            $stmt->execute([':userId' => $userId]);
            $profits = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Fetch the earliest and latest months
            $dateStmt = $this->db->prepare("
            SELECT 
                MIN(DATE_FORMAT(closed_at, '%Y-%m')) AS minMonth,
                MAX(DATE_FORMAT(closed_at, '%Y-%m')) AS maxMonth
            FROM investments
            WHERE user_id = :userId AND status = 'closed'
        ");
            $dateStmt->execute([':userId' => $userId]);
            $dateRange = $dateStmt->fetch(PDO::FETCH_ASSOC);

            if (!$dateRange['minMonth'] || !$dateRange['maxMonth']) {
                return []; // No data available
            }

            // Generate all months between minMonth and maxMonth
            $allMonths = [];
            $currentDate = new DateTime($dateRange['minMonth']);
            $endDate = new DateTime($dateRange['maxMonth']);
            while ($currentDate <= $endDate) {
                $allMonths[$currentDate->format('Y-m')] = 0; // Default profit: 0
                $currentDate->modify('+1 month');
            }

            // Map profits to months
            foreach ($profits as $profit) {
                $allMonths[$profit['month']] = (float)$profit['monthlyProfit'];
            }

            // Prepare the result with "no change" logic
            $result = [];
            $previousProfit = 0;
            foreach ($allMonths as $month => $monthlyProfit) {
                if ($monthlyProfit === 0) {
                    $result[] = ['month' => $month, 'monthlyProfit' => $previousProfit];
                } else {
                    $result[] = ['month' => $month, 'monthlyProfit' => $monthlyProfit];
                    $previousProfit = $monthlyProfit;
                }
            }

            return $result;
        } catch (Exception $e) {
            error_log("Error fetching monthly profits: " . $e->getMessage());
            return [];
        }
    }


    /**
     * Calculate derived metrics based on overall statistics.
     *
     * @param array $stats
     * @return array
     */
    private function calculateDerivedMetrics(array $stats): array
    {
        $totalTrades = $stats['totalTrades'] ?? 0;
        $winningTrades = $stats['winningTrades'] ?? 0;
        $totalGains = $stats['totalGains'] ?? 0;
        $totalLosses = $stats['totalLosses'] ?? 0;

        $successRate = $totalTrades > 0 ? ($winningTrades / $totalTrades) * 100 : 0;
        $profitFactor = $totalLosses > 0 ? ($totalGains / $totalLosses) : 0;

        return [
            'successRate'    => round($successRate, 2),
            'profitFactor'   => round($profitFactor, 2)
        ];
    }

    /**
     * Get comprehensive trade metrics for a user.
     *
     * @param int $userId
     * @return array
     * @throws Exception
     */
    public function getTradeMetrics(int $userId): array
    {
        try {
            // Fetch overall statistics
            $stats = $this->fetchOverallTradeStatistics($userId);

            // Fetch monthly profits to determine best and worst months
            $monthlyResults = $this->fetchMonthlyProfits($userId);
            $monthlyProfits = array_column($monthlyResults, 'monthlyProfit');

            // Calculate derived metrics
            $derivedMetrics = $this->calculateDerivedMetrics($stats);

            // Determine best and worst month profits
            $bestMonthProfit = !empty($monthlyProfits) ? max($monthlyProfits) : 0;
            $worstMonthLoss  = !empty($monthlyProfits) ? min($monthlyProfits) : 0;

            // Calculate risk/reward ratio
            $riskRewardRatio = isset($derivedMetrics['profitFactor']) ? $derivedMetrics['profitFactor'] : 0;

            return array_merge($stats, $derivedMetrics, [
                'bestMonthProfit'      => round($bestMonthProfit, 2),
                'worstMonthLoss'       => round($worstMonthLoss, 2),
                'riskRewardRatio'      => round($riskRewardRatio, 2),
                'avgHoldTime'          => $this->calculateAverageHoldTime($userId),
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
    private function calculateAverageHoldTime(int $userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT AVG(DATEDIFF(closed_at, created_at)) AS avgHoldTime
                FROM investments
                WHERE user_id = :userId AND status = 'closed'
            ");
            $stmt->execute([':userId' => $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return round($result['avgHoldTime'] ?? 0, 2);
        } catch (Exception $e) {
            error_log("Error calculating average hold time: " . $e->getMessage());
            return 0;
        }
    }
}
?>
