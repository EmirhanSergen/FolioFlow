<?php
class Investment {
    private $db;
    private $symbol;

    public function __construct($database, Symbol $symbol) {
        $this->db = $database;
        $this->symbol = $symbol;
    }

    // Fetches all active investments for a user from the database.
    // Includes details like profit/loss and other calculated fields.
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
                END as profit_loss,
                COALESCE(logs.is_averaged, 0) as is_averaged,
                COALESCE(logs.total_added_amount, 0) as total_added_amount
                FROM investments i
                LEFT JOIN symbols s ON i.name = s.symbol
                LEFT JOIN (
                    SELECT 
                        investment_id, 
                        COUNT(*) > 0 as is_averaged, 
                        SUM(added_amount) as total_added_amount
                    FROM investment_logs
                    GROUP BY investment_id
                ) logs ON i.id = logs.investment_id
                WHERE i.user_id = ? 
                AND i.status = 'active';
            ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    // Updates the prices of the provided symbols if forceUpdate is true or they need updating.
    private function updatePricesIfNeeded($symbols, $forceUpdate) {
        if ($forceUpdate && !empty($symbols)) {
            $this->symbol->updatePrices($symbols,$forceUpdate);
        }
    }

    // Re-fetches the investments after updates to ensure fresh and accurate data.
    private function refetchInvestments($userId) {
        return $this->fetchInvestments($userId);
    }

    // 1. Fetches investments for the user.
    // 2. Updates symbol prices if necessary.
    // 3. Returns the final updated list of investments.
    public function getAllOpenInvestments($userId, $forceUpdate = false) {
        $investments = $this->fetchInvestments($userId);
        $symbols = array_unique(array_column($investments, 'name'));

        $this->updatePricesIfNeeded($symbols, $forceUpdate);

        if ($forceUpdate) {
            $investments = $this->refetchInvestments($userId);
        }
        return $investments;
    }

    private function updateExistingInvestment($existingInvestment, $data, $userId) {
        // Calculate the new average buy price
        $newAverageBuyPrice = $this->calculateAverageBuyPrice(
            $existingInvestment['amount'],
            $existingInvestment['buy_price'],
            $data['amount'],
            $data['buy_price']
        );

        // Update the investment record
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

        // Log the investment update
        $this->logInvestmentUpdate($existingInvestment['id'], [
            'previous_amount' => $existingInvestment['amount'],
            'added_amount' => $data['amount'],
            'previous_buy_price' => $existingInvestment['buy_price'],
            'new_buy_price' => $newAverageBuyPrice,
            'user_id' => $userId
        ]);
    }

    private function addNewInvestment($data, $userId) {
        // Add symbol if it doesn't exist (handled by addSymbol function)
        $this->symbol->addSymbol($data['name']);

        // Insert the new investment
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

    private function calculateAverageBuyPrice($existingAmount, $existingPrice, $newAmount, $newPrice) {
        $totalCurrentValue = $existingAmount * $existingPrice;
        $newValue = $newAmount * $newPrice;
        $totalAmount = $existingAmount + $newAmount;
        return ($totalCurrentValue + $newValue) / $totalAmount;
    }

    public function addInvestment($data, $userId) {
        try {
            // Check if the user already has an active investment for the given symbol
            $stmt = $this->db->prepare("
            SELECT id, amount, buy_price 
            FROM investments 
            WHERE user_id = ? 
            AND name = ? 
            AND status = 'active'
        ");
            $stmt->execute([$userId, $data['name']]);
            $existingInvestment = $stmt->fetch();

            $this->db->beginTransaction();

            if ($existingInvestment) {
                // Update existing investment
                $this->updateExistingInvestment($existingInvestment, $data, $userId);
            } else {
                // Add new investment
                $this->addNewInvestment($data, $userId);
            }

            // Ensure the symbol's price is up to date
            $this->symbol->updatePrice($data['name']);

            $this->db->commit();
            return [
                'success' => true,
                'is_update' => !empty($existingInvestment),
                'investment' => $existingInvestment ?? [
                        'name' => $data['name'],
                        'amount' => $data['amount'],
                        'buy_price' => $data['buy_price']
                    ]
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("[Investment] Error adding investment for user $userId with symbol {$data['name']}: " . $e->getMessage());
            throw $e;
        }
    }

    public function closeInvestment($id, $sellPrice) {
        try {
            $this->db->beginTransaction();

            // Get current investment data
            $stmt = $this->db->prepare("
                SELECT name FROM investments WHERE id = ?
            ");
            $stmt->execute([$id]);
            $investment = $stmt->fetch();

            // Update price one last time before closing
            if ($investment) {
                $this->symbol->updatePrice($investment['name']);
            }

            // Close the investment
            $stmt = $this->db->prepare("
                UPDATE investments 
                SET status = 'closed', 
                    sell_price = ?, 
                    closed_at = NOW()
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

    public function getInvestmentByUserId($id, $userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    i.*,
                    s.current_price,
                    s.last_updated,
                    COALESCE(logs.is_averaged, 0) as is_averaged,
                    COALESCE(logs.total_added_amount, 0) as total_added_amount
                FROM investments i
                LEFT JOIN symbols s ON i.name = s.symbol
                LEFT JOIN (
                    SELECT 
                        investment_id, 
                        COUNT(*) > 0 as is_averaged, 
                        SUM(added_amount) as total_added_amount
                    FROM investment_logs
                    GROUP BY investment_id
                ) logs ON i.id = logs.investment_id
                WHERE i.id = ? AND i.user_id = ?
            ");

            $stmt->execute([$id, $userId]);
            $investment = $stmt->fetch();

            // Update price if needed
            if ($investment && $this->symbol->needsPriceUpdate($investment['name'])) {
                $this->symbol->updatePrice($investment['name']);
                // Fetch fresh data
                $stmt->execute([$id, $userId]);
                $investment = $stmt->fetch();
            }

            return $investment;
        } catch (Exception $e) {
            error_log("[Investment] Error getting investment: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateInvestment($id, $userId, $data) {
        try {
            // Validate symbol format ??
            if (strpos($data['name'], 'USDT') === false) {
                throw new Exception("Invalid crypto symbol format. Must end with USDT");
            }

            $this->db->beginTransaction();

            // First add the symbol if it's new
            $this->symbol->addSymbol($data['name']);

            $stmt = $this->db->prepare("
                UPDATE investments 
                SET name = ?, 
                    buy_price = ?, 
                    amount = ?
                WHERE id = ? AND user_id = ?
            ");

            $success = $stmt->execute([
                $data['name'],
                $data['buy_price'],
                $data['amount'],
                $id,
                $userId
            ]);

            // Update price for the new symbol if changed
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

    private function logInvestmentUpdate($investmentId, $data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO investment_logs (
                    investment_id,
                    previous_amount,
                    added_amount,
                    previous_buy_price,
                    new_buy_price,
                    user_id,
                    created_at
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