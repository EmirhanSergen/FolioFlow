<?php

// Website Routes as key and pairs
$routes = [
    '' => 'controllers/home.php',  // Home page
    'login' => 'controllers/login.php', // Login page
    'register' => 'controllers/register.php', // Registration page
    'logout' => 'controllers/logout.php', // Logout functionality
    'dashboard' => 'controllers/dashboard.php', // Dashboard (logged-in user)
    'investments' => 'controllers/investments.php', // List of investments
    'investment' => 'controllers/investment.php', // Single investment details
    'add-investment' => 'controllers/add-investment.php', // Add new investment
    'closed-positions' => 'controllers/closed-positions.php', // View closed positions
    'close-position' => 'controllers/close-position.php', // Close an investment
    'analytics' => 'controllers/analytics.php', // Analytics and metrics
    'documentation' => 'controllers/documentation.php', // Documentation page
];

/**
 * Handles route dispatching by URI path.
 */
function routeToController($uri, $routes){

    if (array_key_exists($uri, $routes)) {
        require $routes[$uri];
    } else {
        abort();
    }
}


/**
 * Show error view and exit.
 */
function abort($code = 404 ){
    http_response_code($code);

    require "views/error.view.php";

    die();
}
?>
