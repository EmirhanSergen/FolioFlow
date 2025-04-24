<?php
/**
 * Dashboard class for see overall investments .
 */
class Dashboard {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    /**
     * Get the count of active investments for a user.
     */
    public function getActiveInvestmentCount($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total 
                FROM investments 
                WHERE user_id = ? AND status = 'active'
            ");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) ($result['total'] ?? 0);
        } catch (Exception $e) {
            error_log("[Dashboard] Error fetching active investment count: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Calculate the total invested amount and current portfolio value for active investments.
     */
    public function calculateTotalInvestmentByUserId($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COALESCE(SUM(i.buy_price * i.amount), 0) AS total_investment,
                    COALESCE(SUM(s.current_price * i.amount), 0) AS current_value
                FROM investments i
                JOIN symbols s ON i.name = s.symbol
                WHERE i.user_id = ? AND i.status = 'active'
            ");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return [
                'total_investment' => (float) ($result['total_investment'] ?? 0),
                'current_value' => (float) ($result['current_value'] ?? 0)
            ];
        } catch (Exception $e) {
            error_log("[Dashboard] Error calculating total investment: " . $e->getMessage());
            return [
                'total_investment' => 0,
                'current_value' => 0
            ];
        }
    }

    /**
     * Calculate the total profit/loss for active investments.
     */
    public function calculateTotalProfitByUserId($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COALESCE(SUM((s.current_price - i.buy_price) * i.amount), 0) AS total_profit
                FROM investments i
                JOIN symbols s ON i.name = s.symbol
                WHERE i.user_id = ? AND i.status = 'active' AND s.current_price IS NOT NULL
            ");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (float) ($result['total_profit'] ?? 0);
        } catch (Exception $e) {
            error_log("[Dashboard] Error calculating total profit: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get the best and worst performing active investments based on ROI.
     */
    public function getPerformanceExtremes($userId) {
        try {
            // Best performing investment
            $stmt = $this->db->prepare("
                SELECT 
                    i.id,
                    s.symbol,
                    s.name,
                    i.amount,
                    i.buy_price,
                    s.current_price,
                    ROUND(((s.current_price - i.buy_price) / i.buy_price) * 100, 2) AS return_percentage
                FROM investments i
                JOIN symbols s ON i.name = s.symbol
                WHERE i.user_id = ? 
                    AND i.status = 'active' 
                    AND s.current_price IS NOT NULL
                ORDER BY return_percentage DESC
                LIMIT 1
            ");
            $stmt->execute([$userId]);
            $best = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

            // Worst performing investment
            $stmt = $this->db->prepare("
                SELECT 
                    i.id,
                    s.symbol,
                    s.name,
                    i.amount,
                    i.buy_price,
                    s.current_price,
                    ROUND(((s.current_price - i.buy_price) / i.buy_price) * 100, 2) AS return_percentage
                FROM investments i
                JOIN symbols s ON i.name = s.symbol
                WHERE i.user_id = ? 
                    AND i.status = 'active' 
                    AND s.current_price IS NOT NULL
                ORDER BY return_percentage ASC
                LIMIT 1
            ");
            $stmt->execute([$userId]);
            $worst = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

            return [
                'best' => $best,
                'worst' => $worst
            ];
        } catch (Exception $e) {
            error_log("[Dashboard] Error fetching performance extremes: " . $e->getMessage());
            return [
                'best' => null,
                'worst' => null
            ];
        }
    }

    /**
     * Calculate the Return on Investment (ROI) for active investments.
     */
    public function calculateROI($userId) {
        try {
            // Retrieve total investment and current value
            $investmentData = $this->calculateTotalInvestmentByUserId($userId);
            $totalInvestment = $investmentData['total_investment'];
            $currentValue = $investmentData['current_value'];

            if ($totalInvestment == 0) {
                return 0;
            }

            // Calculate profit/loss
            $profitLoss = $currentValue - $totalInvestment;

            // Calculate ROI
            $roi = ($profitLoss / $totalInvestment) * 100;

            return round($roi, 2);
        } catch (Exception $e) {
            error_log("[Dashboard] Error calculating ROI: " . $e->getMessage());
            return 0;
        }
    }
}
?>
