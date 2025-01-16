<?php

class Dashboard {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    // Get active investment count (replacing getInvestmentCount as we want active only)
    public function getActiveInvestmentCount($userId) {
        try {
            $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM investments 
            WHERE user_id = ? AND status = 'active'
        ");
            $stmt->execute([$userId]);
            return (int) $stmt->fetch()['total'] ?? 0;
        } catch (Exception $e) {
            error_log("[Dashboard] Error fetching active investment count: " . $e->getMessage());
            return 0;
        }
    }

    // Calculate total investment and current value (enhanced version of calculateTotalInvestmentByUserId)
    public function calculateTotalInvestmentByUserId($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COALESCE(SUM(buy_price * amount), 0) as total_investment,
                    COALESCE(SUM(
                        CASE 
                            WHEN current_price IS NOT NULL 
                            THEN (current_price * amount) 
                            ELSE (buy_price * amount) 
                        END
                    ), 0) as current_value
                FROM investments i
                LEFT JOIN symbols s ON i.name = s.symbol
                WHERE i.user_id = ? AND i.status = 'active'
            ");
            $stmt->execute([$userId]);
            $result = $stmt->fetch();
            return [
                'total_investment' => (float) $result['total_investment'],
                'current_value' => (float) $result['current_value']
            ];
        } catch (Exception $e) {
            error_log("[Dashboard] Error calculating total investment: " . $e->getMessage());
            return [
                'total_investment' => 0,
                'current_value' => 0
            ];
        }
    }

    // Calculate total profit from both active and closed investments (enhanced version of calculateProfitByUserId)
    public function calculateTotalProfitByUserId($userId) {
        try {
            $stmt = $this->db->prepare("
            SELECT 
                COALESCE(SUM(
                    CASE 
                        WHEN status = 'closed' 
                        THEN (sell_price - buy_price) * amount
                        WHEN status = 'active' AND current_price IS NOT NULL
                        THEN (current_price - buy_price) * amount
                        ELSE 0
                    END
                ), 0) as total_profit
            FROM investments i
            LEFT JOIN symbols s ON i.name = s.symbol
            WHERE i.user_id = ?
        ");
            $stmt->execute([$userId]);
            return (float) $stmt->fetch()['total_profit'];
        } catch (Exception $e) {
            error_log("[Dashboard] Error calculating total profit: " . $e->getMessage());
            return 0;
        }
    }

    // Get best and worst performing active investments
    public function getPerformanceExtremes($userId) {
        // Best performing
        $stmt = $this->db->prepare("
        SELECT 
            i.*,
            s.current_price,
            ((s.current_price - i.buy_price) / i.buy_price * 100) as return_percentage
        FROM investments i
        LEFT JOIN symbols s ON i.name = s.symbol
        WHERE i.user_id = ? 
            AND i.status = 'active' 
            AND s.current_price IS NOT NULL
        ORDER BY return_percentage DESC
        LIMIT 1
    ");
        $stmt->execute([$userId]);
        $best = $stmt->fetch();

        // Worst performing
        $stmt = $this->db->prepare("
        SELECT 
            i.*,
            s.current_price,
            ((s.current_price - i.buy_price) / i.buy_price * 100) as return_percentage
        FROM investments i
        LEFT JOIN symbols s ON i.name = s.symbol
        WHERE i.user_id = ? 
            AND i.status = 'active' 
            AND s.current_price IS NOT NULL
        ORDER BY return_percentage ASC
        LIMIT 1
    ");
        $stmt->execute([$userId]);
        $worst = $stmt->fetch();

        return [
            'best' => $best,
            'worst' => $worst
        ];
    }



    // Calculate ROI including both active and closed positions (new function)
    public function calculateROI($userId) {
        // Get total investment amount (both active and closed)
        $stmt = $this->db->prepare("
        SELECT 
            SUM(buy_price * amount) as total_investment
        FROM investments 
        WHERE user_id = ?
    ");
        $stmt->execute([$userId]);
        $totalInvestment = $stmt->fetch()['total_investment'] ?? 0;

        // Get total profit
        $totalProfit = $this->calculateTotalProfitByUserId($userId);

        // Calculate ROI
        return $totalInvestment > 0 ? ($totalProfit / $totalInvestment) * 100 : 0;
    }
}