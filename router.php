<?php

// Website Routes as key and pairs
$routes = [
    '' => 'controllers/home.php',
    'login' => 'controllers/login.php',
    'register' => 'controllers/register.php',
    'logout' => 'controllers/logout.php',
    'dashboard' => 'controllers/dashboard.php',
    'investments' => 'controllers/investments.php',
    'investment' => 'controllers/investment.php',
    'add-investment' => 'controllers/add-investment.php',
    'closed-positions' => 'controllers/closed-positions.php',
    'close-position' => 'controllers/close-position.php'
];


//
function routeToController($uri,$routes){

    if (array_key_exists($uri, $routes)) {
        require $routes[$uri];
    } else {
        abort();
    }
}

// Error handling
function abort($code = 404 ){
    http_response_code(404);

    require "views/{$code}.view.php";

    die();
}