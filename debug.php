<?php
// Add this to your functions.php or create a new debug.php file

function debug($data, $label = '') {
    $output = "\n------------------------\n";
    $output .= $label ? "[$label]\n" : '';
    $output .= print_r($data, true);
    $output .= "\n------------------------\n";

    error_log($output);

    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        echo "<pre>";
        echo htmlspecialchars($output);
        echo "</pre>";
    }
}

// Usage in controller:
debug($_POST, 'POST Data');
debug($availableCryptos, 'Available Cryptos');