<?php
/**
 * Class Investment
 * Handles investment CRUD operations, price updates, and profit/loss calculations.
 */
class Investment {
    /** @var PDO Database connection */
    private $db;

    /** @var Symbol Symbol price handling service */
    private $symbol;

    /**
     * Constructor.
     */
    public function __construct($database, Symbol $symbol) {
        $this->db = $database;
        $this->symbol = $symbol;
    }

    /**
     * Fetch all active investments for the given user.
     * Includes profit/loss and metadata from investment logs.
     */
    private function fetchInvestments($userId) {
        $query = "
            SELECT 
                i.*, 
                s.current_price, 
                s.last_updated, 
                CASE 
                    WHEN s.current_price IS NOT NULL 
                    THEN (s.current_price - i.buy_price) * i.amount 
                    ELSE 0 
                END AS profit_loss,
                COALESCE(logs.is_averaged, 0) AS is_averaged,
                COALESCE(logs.total_added_amount, 0) AS total_added_amount
            FROM investments i
            LEFT JOIN symbols s ON i.name = s.symbol
            LEFT JOIN (
                SELECT 
                    investment_id, 
                    COUNT(*) > 0 AS is_averaged, 
                    SUM(added_amount) AS total_added_amount
                FROM investment_logs
                GROUP BY investment_id
            ) logs ON i.id = logs.investment_id
            WHERE i.user_id = ? AND i.status = 'active';
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Trigger symbol price updates if forced or required.
     */
    private function updatePricesIfNeeded($symbols, $forceUpdate) {
        if ($forceUpdate && !empty($symbols)) {
            $this->symbol->updatePrices($symbols, $forceUpdate);
        }
    }

    /**
     * Refetch fresh investment data.
     */
    private function refetchInvestments($userId) {
        return $this->fetchInvestments($userId);
    }

    /**
     * Get all active investments for a user, optionally forcing symbol price update.
     */
    public function getAllOpenInvestments($userId, $forceUpdate = false) {
        $investments = $this->fetchInvestments($userId);
        $symbols = array_unique(array_column($investments, 'name'));

        $this->updatePricesIfNeeded($symbols, $forceUpdate);

        if ($forceUpdate) {
            $investments = $this->refetchInvestments($userId);
        }

        return $investments;
    }

    /**
     * Update existing investment with new buy price and amount (averaging).
     */
    private function updateExistingInvestment($existingInvestment, $data, $userId) {
        $newAverageBuyPrice = $this->calculateAverageBuyPrice(
            $existingInvestment['amount'],
            $existingInvestment['buy_price'],
            $data['amount'],
            $data['buy_price']
        );

        $updateStmt = $this->db->prepare("
            UPDATE investments 
            SET amount = amount + ?, buy_price = ? 
            WHERE id = ?
        ");
        $updateStmt->execute([
            $data['amount'],
            $newAverageBuyPrice,
            $existingInvestment['id']
        ]);

        $this->logInvestmentUpdate($existingInvestment['id'], [
            'previous_amount' => $existingInvestment['amount'],
            'added_amount' => $data['amount'],
            'previous_buy_price' => $existingInvestment['buy_price'],
            'new_buy_price' => $newAverageBuyPrice,
            'user_id' => $userId
        ]);
    }

    /**
     * Add a new investment record for a user.
     */
    private function addNewInvestment($data, $userId) {
        $this->symbol->addSymbol($data['name']);

        $stmt = $this->db->prepare("
            INSERT INTO investments (name, buy_price, amount, user_id, status) 
            VALUES (?, ?, ?, ?, 'active')
        ");
        $stmt->execute([
            $data['name'],
            $data['buy_price'],
            $data['amount'],
            $userId
        ]);
    }

    /**
     * Calculate new average buy price when adding more to an existing position.
     */
    private function calculateAverageBuyPrice($existingAmount, $existingPrice, $newAmount, $newPrice) {
        $totalCurrentValue = $existingAmount * $existingPrice;
        $newValue = $newAmount * $newPrice;
        $totalAmount = $existingAmount + $newAmount;
        return ($totalCurrentValue + $newValue) / $totalAmount;
    }

    /**
     * Public method to add (or update) an investment for a user.
     */
    public function addInvestment($data, $userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT id, amount, buy_price 
                FROM investments 
                WHERE user_id = ? AND name = ? AND status = 'active'
            ");
            $stmt->execute([$userId, $data['name']]);
            $existingInvestment = $stmt->fetch();

            $this->db->beginTransaction();

            if ($existingInvestment) {
                $this->updateExistingInvestment($existingInvestment, $data, $userId);
            } else {
                $this->addNewInvestment($data, $userId);
            }

            $this->symbol->updatePrice($data['name']);
            $this->db->commit();

            return [
                'success' => true,
                'is_update' => !empty($existingInvestment),
                'investment' => $existingInvestment ?? $data
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("[Investment] Error adding investment for user $userId with symbol {$data['name']}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mark an investment as closed and store the sell price.
     */
    public function closeInvestment($id, $sellPrice) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("SELECT name FROM investments WHERE id = ?");
            $stmt->execute([$id]);
            $investment = $stmt->fetch();

            if ($investment) {
                $this->symbol->updatePrice($investment['name']);
            }

            $stmt = $this->db->prepare("
                UPDATE investments 
                SET status = 'closed', sell_price = ?, closed_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$sellPrice, $id]);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("[Investment] Error closing investment: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Fetch a specific investment by ID and user.
     * Refreshes price if outdated.
     */
    public function getInvestmentByUserId($id, $userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    i.*, 
                    s.current_price, 
                    s.last_updated,
                    COALESCE(logs.is_averaged, 0) AS is_averaged,
                    COALESCE(logs.total_added_amount, 0) AS total_added_amount
                FROM investments i
                LEFT JOIN symbols s ON i.name = s.symbol
                LEFT JOIN (
                    SELECT investment_id, COUNT(*) > 0 AS is_averaged, SUM(added_amount) AS total_added_amount
                    FROM investment_logs
                    GROUP BY investment_id
                ) logs ON i.id = logs.investment_id
                WHERE i.id = ? AND i.user_id = ?
            ");
            $stmt->execute([$id, $userId]);
            $investment = $stmt->fetch();

            if ($investment && $this->symbol->needsPriceUpdate($investment['name'])) {
                $this->symbol->updatePrice($investment['name']);
                $stmt->execute([$id, $userId]);
                $investment = $stmt->fetch();
            }

            return $investment;
        } catch (Exception $e) {
            error_log("[Investment] Error getting investment: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update an existing investment record.
     * Includes symbol format validation and optional symbol price update.
     */
    public function updateInvestment($id, $userId, $data) {
        try {
            if (strpos($data['name'], 'USDT') === false) {
                throw new Exception("Invalid crypto symbol format. Must end with USDT");
            }

            $this->db->beginTransaction();
            $this->symbol->addSymbol($data['name']);

            $stmt = $this->db->prepare("
                UPDATE investments 
                SET name = ?, buy_price = ?, amount = ?
                WHERE id = ? AND user_id = ?
            ");
            $success = $stmt->execute([
                $data['name'], $data['buy_price'], $data['amount'], $id, $userId
            ]);

            if ($success) {
                $this->symbol->updatePrice($data['name']);
            }

            $this->db->commit();
            return $success;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("[Investment] Error updating investment: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Log an update event to investment_logs.
     */
    private function logInvestmentUpdate($investmentId, $data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO investment_logs (
                    investment_id, previous_amount, added_amount,
                    previous_buy_price, new_buy_price, user_id, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            return $stmt->execute([
                $investmentId,
                $data['previous_amount'],
                $data['added_amount'],
                $data['previous_buy_price'],
                $data['new_buy_price'],
                $data['user_id']
            ]);
        } catch (Exception $e) {
            error_log("[Investment] Error logging investment update: " . $e->getMessage());
            return false;
        }
    }
}
