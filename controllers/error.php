<?php

function renderError($code) {
    $errors = [
        '404' => [
            'title' => 'Page Not Found',
            'message' => "The page you're looking for doesn't exist or has been moved."
        ],
        '403' => [
        'title' => 'Access Forbidden',
        'message' => 'You don\'t have permission to access this page.'
    ],
        // Add more error types as needed
    ];

    $error = $errors[$code] ?? [
        'title' => 'Error',
        'message' => 'An unexpected error occurred.'
    ];

    http_response_code($code);
    require 'views/error.view.php';
}