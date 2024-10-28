<?php
// Create logs directory if it doesn't exist
$logDir = __DIR__ . '/logs';
if (!file_exists($logDir)) {
    mkdir($logDir, 0777, true);
}

// Set error log path
ini_set('error_log', $logDir . '/error.log');
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();

require 'classes/Database.php';
require 'functions.php';
require 'router.php';

try {
    $config = require("config/config.php");
    $db = new Database($config['database']);

    // Store database instance in global scope for access in controllers
    $GLOBALS['db'] = $db;

    // Parse URL and remove "FolioFlow"
    $uri = parse_url($_SERVER['REQUEST_URI'])['path'];
    $uri = str_replace('/FolioFlow/', '', $uri);

    routeToController($uri, $routes);
} catch (Exception $e) {
    // Log the error
    error_log("Application Error: " . $e->getMessage());

    // Show error page
    http_response_code(500);
    require 'views/500.view.php';
    exit;
}