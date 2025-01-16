<?php
class Investment {
    private $db;
    private $symbol;

    public function __construct($database, Symbol $symbol) {
        $this->db = $database;
        $this->symbol = $symbol;
    }

    // Get all active crypto investments with averaging info
    public function getAllOpenInvestments($userId, $forceUpdate = false) {
        try {
            $query = "
                SELECT 
                    i.*, -- Fetches all columns from the `investments` table for each investment.
                    s.current_price, -- Retrieves the current price of the symbol from the `symbols` table.
                    s.last_updated, -- Retrieves the last updated timestamp of the symbol's price.
                    CASE 
                        WHEN s.current_price IS NOT NULL 
                        THEN (s.current_price - i.buy_price) * i.amount 
                        ELSE 0 
                    END as profit_loss,
                    (SELECT COUNT(*) > 0 
                     FROM investment_logs 
                     WHERE investment_id = i.id) as is_averaged,
                    (SELECT SUM(added_amount) 
                     FROM investment_logs 
                     WHERE investment_id = i.id) as total_added_amount
                FROM investments i
                LEFT JOIN symbols s ON i.name = s.symbol
                WHERE i.user_id = ? 
                AND i.status = 'active'
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId]);
            $investments = $stmt->fetchAll();

            // Get unique symbols for  update
            $symbols = array_unique(array_column($investments, 'name'));

            // If updates are forced or needed, refresh prices
            if ($forceUpdate && !empty($symbols)) {
                $this->symbol->updatePrices($symbols); // Batch update prices

                // Re-fetch investments after the price update
                $stmt->execute([$userId]);
                $investments = $stmt->fetchAll();
            }

            return $investments;
        } catch (Exception $e) {
            error_log("[Investment] Error getting investments: " . $e->getMessage());
            throw $e;
        }
    }


    public function addInvestment($data, $userId) {
        try {
            // First check if user already has this investment
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
                // Calculate new average buy price
                $totalCurrentValue = $existingInvestment['amount'] * $existingInvestment['buy_price'];
                $newValue = $data['amount'] * $data['buy_price'];
                $totalAmount = $existingInvestment['amount'] + $data['amount'];
                $newAverageBuyPrice = ($totalCurrentValue + $newValue) / $totalAmount;

                // Update existing investment
                $updateStmt = $this->db->prepare("
                    UPDATE investments 
                    SET amount = amount + ?,
                        buy_price = ?
                    WHERE id = ?
                ");

                $success = $updateStmt->execute([
                    $data['amount'],
                    $newAverageBuyPrice,
                    $existingInvestment['id']
                ]);

                if ($success) {
                    // Log the additional investment
                    $this->logInvestmentUpdate($existingInvestment['id'], [
                        'previous_amount' => $existingInvestment['amount'],
                        'added_amount' => $data['amount'],
                        'previous_buy_price' => $existingInvestment['buy_price'],
                        'new_buy_price' => $newAverageBuyPrice,
                        'user_id' => $userId
                    ]);
                }
            } else {
                // Add to symbols table
                $this->symbol->addSymbol($data['name']);

                // Add investment
                $stmt = $this->db->prepare("
                    INSERT INTO investments (
                        name, 
                        buy_price, 
                        amount, 
                        user_id, 
                        status
                    ) VALUES (?, ?, ?, ?, 'active')
                ");

                $success = $stmt->execute([
                    $data['name'],
                    $data['buy_price'],
                    $data['amount'],
                    $userId
                ]);
            }

            // Get initial price
            $this->symbol->updatePrice($data['name']);

            $this->db->commit();
            return [
                'success' => true,
                'is_update' => !empty($existingInvestment)
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("[Investment] Error adding investment: " . $e->getMessage());
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

    public function getInvestmentById($id, $userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    i.*,
                    s.current_price,
                    s.last_updated,
                    (SELECT COUNT(*) > 0 
                     FROM investment_logs 
                     WHERE investment_id = i.id) as is_averaged,
                    (SELECT SUM(added_amount) 
                     FROM investment_logs 
                     WHERE investment_id = i.id) as total_added_amount
                FROM investments i
                LEFT JOIN symbols s ON i.name = s.symbol
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