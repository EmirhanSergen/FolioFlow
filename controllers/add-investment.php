<?php
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Symbol.php';
require_once __DIR__ . '/../classes/Investment.php';

checkAuth();

$errors = [];
$success = '';
$investmentType = $_POST['investment_type'] ?? '';

try {
    // Initialize database and dependencies
    $config = require __DIR__ . '/../config/config.php';
    $db = new Database($config['database']);
    $symbol = new Symbol($db);
    $investment = new Investment($db, $symbol);

    // Fetch available cryptocurrencies
    $stmt = $db->prepare("
        SELECT symbol, name 
        FROM symbols 
        WHERE type = 'crypto'
        ORDER BY symbol ASC
    ");
    $stmt->execute();
    $availableCryptos = $stmt->fetchAll();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Collect form data
        $symbolName = strtoupper(trim($_POST['symbol'] ?? ''));
        $buyPrice = filter_var($_POST['buy_price'] ?? 0, FILTER_VALIDATE_FLOAT);
        $amount = filter_var($_POST['amount'] ?? 0, FILTER_VALIDATE_FLOAT);

        // Validation
        if (empty($investmentType)) {
            $errors['investment_type'] = 'Please select investment type';
        }

        if (empty($symbolName)) {
            $errors['symbol'] = 'Symbol is required';
        }

        if ($buyPrice <= 0) {
            $errors['buy_price'] = 'Please enter a valid buy price';
        }

        if ($amount <= 0) {
            $errors['amount'] = 'Please enter a valid amount';
        }

        // If no errors, proceed with adding/updating investment
        if (empty($errors)) {
            try {
                // For crypto, ensure it ends with USDT
                if ($investmentType === 'crypto' && !str_ends_with($symbolName, 'USDT')) {
                    $symbolName .= 'USDT';
                }

                $result = $investment->addInvestment([
                    'name' => $symbolName,
                    'buy_price' => $buyPrice,
                    'amount' => $amount
                ], $_SESSION['user_id']);

                if ($result['success']) {
                    if ($result['is_update']) {
                        $_SESSION['success_message'] = "Investment updated successfully! Average buy price has been recalculated.";
                    } else {
                        $_SESSION['success_message'] = "Investment added successfully!";
                    }
                    header('Location: /FolioFlow/investments');
                    exit();
                }
            } catch(Exception $e) {
                error_log("Error adding investment: " . $e->getMessage());
                $errors['general'] = "Error adding investment. Please try again.";
            }
        }
    }
} catch(Exception $e) {
    error_log("Error in add-investment controller: " . $e->getMessage());
    $errors['general'] = "An error occurred. Please try again later.";
    $availableCryptos = [];
}

require 'views/add-investment.view.php';