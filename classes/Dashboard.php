<?php

class Dashboard {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    // Get active investment count (replacing getInvestmentCount as we want active only)
    public function getActiveInvestmentCount($userId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM investments 
            WHERE user_id = ? AND status = 'active'
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch()['total'];
    }

    // Calculate total investment and current value (enhanced version of calculateTotalInvestmentByUserId)
    public function calculateTotalInvestmentByUserId($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                SUM(buy_price * amount) as total_investment,
                SUM(
                    CASE 
                        WHEN current_price IS NOT NULL 
                        THEN (current_price * amount) 
                        ELSE (buy_price * amount) 
                    END
                ) as current_value
            FROM investments i
            LEFT JOIN symbols s ON i.name = s.symbol
            WHERE i.user_id = ? AND i.status = 'active'
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    // Calculate total profit from both active and closed investments (enhanced version of calculateProfitByUserId)
    public function calculateTotalProfitByUserId($userId) {
        // First get profit from closed positions
        $closedStmt = $this->db->prepare("
            SELECT SUM((sell_price - buy_price) * amount) as closed_profit
            FROM investments 
            WHERE user_id = ? AND status = 'closed'
        ");
        $closedStmt->execute([$userId]);
        $closedProfit = $closedStmt->fetch()['closed_profit'] ?? 0;

        // Then get profit from active positions
        $activeStmt = $this->db->prepare("
            SELECT SUM((s.current_price - i.buy_price) * i.amount) as active_profit
            FROM investments i
            LEFT JOIN symbols s ON i.name = s.symbol
            WHERE i.user_id = ? AND i.status = 'active' AND s.current_price IS NOT NULL
        ");
        $activeStmt->execute([$userId]);
        $activeProfit = $activeStmt->fetch()['active_profit'] ?? 0;

        return $closedProfit + $activeProfit;
    }

    // Get best and worst performing active investments (new function)
    public function getPerformanceExtremes($userId) {
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