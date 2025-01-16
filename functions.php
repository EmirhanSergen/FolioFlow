<?php

// Debugging utility: Dumps a value and stops the script (dev mode only)
function dd($value) {
    if (getenv('APP_ENV') === 'production') {
        echo "Debugging is disabled in production.";
        die();
    }

    echo "<pre>";
    var_dump($value);
    echo "</pre>";
    die();
}

// Check if the current URL matches the given value (ignores query strings)
function urlIs($value) {
    $currentUrl = strtok($_SERVER['REQUEST_URI'], '?'); // Remove query string
    return rtrim($currentUrl, '/') === rtrim($value, '/'); // Normalize URLs
}
