<?php

class ClosedPosition {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    /**
     * Get the all closed positions before
     */
    public function getAllClosedPositions($userId) {
        $stmt = $this->db->prepare(
            "SELECT name, buy_price, sell_price, 
                    ((sell_price - buy_price)*amount) as profit,
                    closed_at, amount 
             FROM investments 
             WHERE user_id = ? AND status = 'closed'
             ORDER BY closed_at DESC"
        );

        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}