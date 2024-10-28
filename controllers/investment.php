<?php
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Symbol.php';
require_once __DIR__ . '/../classes/Investment.php';

// Check authentication
checkAuth();

$errors = [];
$success = '';

// Initialize database and investment model
$config = require __DIR__ . '/../config/config.php';
$db = new Database($config['database']);
$symbol = new Symbol($db);
$investment = new Investment($db, $symbol);

// Get investment ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    $errors[] = "Invalid investment ID";
    require 'views/investment.view.php';
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        switch($action) {
            case 'update':
                $data = [
                    'name' => strtoupper($_POST['name'] ?? ''),
                    'buy_price' => filter_var($_POST['buy_price'], FILTER_VALIDATE_FLOAT),
                    'amount' => filter_var($_POST['amount'], FILTER_VALIDATE_FLOAT)
                ];

                // Validate data
                if ($data['buy_price'] === false || $data['amount'] === false) {
                    throw new Exception("Invalid price or amount value");
                }

                if (!$data['name']) {
                    throw new Exception("Symbol name is required");
                }

                // Validate crypto symbol format
                if (strpos($data['name'], 'USDT') === false) {
                    throw new Exception("Invalid crypto symbol format. Must end with USDT");
                }

                $investment->updateInvestment($id, $_SESSION['user_id'], $data);
                $_SESSION['success_message'] = "Investment updated successfully!";
                break;

            case 'close':
                $sellPrice = filter_input(INPUT_POST, 'sell_price', FILTER_VALIDATE_FLOAT);
                if ($sellPrice === false || $sellPrice === null) {
                    throw new Exception("Invalid sell price");
                }
                $investment->closeInvestment($id, $sellPrice);
                $_SESSION['success_message'] = "Investment closed successfully!";
                header('Location: /investments');
                exit();

            default:
                $errors[] = "Invalid action";
        }
    } catch(Exception $e) {
        $errors[] = "Error processing request: " . $e->getMessage();
        error_log("[Investment Controller] Error: " . $e->getMessage());
    }
}

// Get investment data
try {
    $investmentData = $investment->getInvestmentById($id, $_SESSION['user_id']);
    if (!$investmentData) {
        $errors[] = "Investment not found";
    } else {
        // Calculate profit/loss
        $profitLoss = 0;
        $profitLossPercent = 0;
        if ($investmentData['current_price']) {
            $profitLoss = ($investmentData['current_price'] - $investmentData['buy_price'])
                * $investmentData['amount'];
            $profitLossPercent = ($investmentData['buy_price'] > 0) ?
                ($profitLoss / ($investmentData['buy_price'] * $investmentData['amount'])) * 100 : 0;
        }
        $investmentData['profit_loss'] = $profitLoss;
        $investmentData['profit_loss_percent'] = $profitLossPercent;
    }
} catch(Exception $e) {
    $errors[] = "Error fetching investment";
    error_log("[Investment Controller] Error: " . $e->getMessage());
}

require 'views/investment.view.php';