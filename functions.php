<?php

/**
 * Debugging utility: dumps a value and halts execution (only in non-production environments).
 */
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

/**
 * Check if the current URL path matches the given route.
 * Ignores query strings and normalizes trailing slashes.
 */
function urlIs($value) {
    $currentUrl = strtok($_SERVER['REQUEST_URI'], '?'); // Remove query string
    return rtrim($currentUrl, '/') === rtrim($value, '/'); // Normalize URLs
}
