<?php

// Load environment variables from .env file if it exists
if (file_exists(__DIR__ . '/../.env')) {
    $envVars = parse_ini_file(__DIR__ . '/../.env');
    foreach ($envVars as $key => $value) {
        putenv("$key=$value"); // Make variable globally available
        $_ENV[$key] = $value; // Store variable in the $_ENV superglobal
    }
}

// Main configuration array returned to the app
return [
    // Database configuration settings
    'database' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'port' => getenv('DB_PORT') ?: '3306',
        'dbname' => getenv('DB_NAME') ?: 'folioflow',
        'charset' => 'utf8mb4',
        'username' => getenv('DB_USERNAME') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
    ],
    // API-related settings
    'api' => [
        'binance' => [
            'endpoint' => 'https://api.binance.com/api/v3',
            'timeout' => 30,
            'user_agent' => 'FolioFlow/1.0'
        ]
    ],
    // Security settings
    'security' => [
        'session_lifetime' => 3600, // Session lifetime in seconds (1 hour)
        'password_algo' => PASSWORD_ARGON2ID, // Password hashing algorithm
    ]
];
