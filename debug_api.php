<?php
require_once __DIR__ . '/classes/APIDebugger.php';

$debugger = new APIDebugger(__DIR__ . '/logs/api_debug.log');

// Test basic connection
$connectionTest = $debugger->testBinanceConnection();
echo "Basic connection test: " . ($connectionTest ? "SUCCESS" : "FAILED") . "\n";

// Test price fetch
$priceTest = $debugger->testSymbolPrice('BTCUSDT');
echo "Price fetch test: " . ($priceTest ? "SUCCESS" : "FAILED") . "\n";