<?php
// classes/Dashboard.php
class Dashboard {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function getInvestmentCount($userId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM investments WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch()['total'];
    }

    public function calculateTotalInvestmentByUserId($userId) {
        $stmt = $this->db->prepare("
            SELECT SUM(buy_price * amount) as total_investment 
            FROM investments 
            WHERE user_id = ? AND status = 'active'
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch()['total_investment'] ?? 0;
    }

    public function calculateProfitByUserId($userId) {
        $stmt = $this->db->prepare("
            SELECT SUM((sell_price - buy_price) * amount) as total_profit 
            FROM investments 
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch()['total_profit'] ?? 0;
    }




}