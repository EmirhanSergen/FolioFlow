<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Symbol.php';
require_once __DIR__ . '/../classes/Investment.php';

/**
 * Middleware to check and update prices for investments.
 */
function checkAndUpdatePrices($userId) {
    // Load configuration and initialize dependencies
    $config = require __DIR__ . '/../config/config.php';
    $db = new Database($config['database']);
    $symbol = new Symbol($db);
    $investment = new Investment($db, $symbol);

    // Fetch investments for the user
    $investments = $investment->getAllOpenInvestments($userId);

    // Check for investments that need price updates
    $symbolsToUpdate = [];
    foreach ($investments as $inv) {
        if (!$inv['last_updated'] || (time() - strtotime($inv['last_updated'])) > 900) { // 15 minutes = 900 seconds
            $symbolsToUpdate[] = $inv['name'];
        }
    }

    // Update prices for symbols that need it
    if (!empty($symbolsToUpdate)) {
        $symbol->updatePrices(array_unique($symbolsToUpdate), true);
    }
}
