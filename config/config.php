<?php

// Load environment variables from .env file if it exists
if (file_exists(__DIR__ . '/../.env')) {
    $envVars = parse_ini_file(__DIR__ . '/../.env');
    foreach ($envVars as $key => $value) {
        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
}

return [
    'database' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'port' => getenv('DB_PORT') ?: '3306',
        'dbname' => getenv('DB_NAME') ?: 'folioflow',
        'charset' => 'utf8mb4',
        'username' => getenv('DB_USERNAME') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
    ],
    'api' => [
        'binance' => [
            'endpoint' => 'https://api.binance.com/api/v3',
            'timeout' => 30,
            'user_agent' => 'FolioFlow/1.0'
        ]
    ],
    'security' => [
        'session_lifetime' => 3600,
        'password_algo' => PASSWORD_ARGON2ID,
    ]
];

return [
    'database' => [
        'host' => 'localhost',
        'port' => '3306',
        'dbname' => 'folioflow',
        'charset' => 'utf8mb4',
    ],
    'api_keys' => [
        'alpha_vantage' => '8RG21IIGE5XFVWBX'  // Your API key
]
];

// First N0SQTBLHZCBTVQ4I