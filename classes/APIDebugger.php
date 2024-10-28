<?php
class APIDebugger {
    private $logFile;

    public function __construct($logFile = null) {
        $this->logFile = $logFile ?? __DIR__ . '/api_debug.log';
    }

    public function testBinanceConnection() {
        $testUrl = "https://api.binance.com/api/v3/ping";

        $this->log("Testing Binance API connection...");

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $testUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; FolioFlow/1.0;)',
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_VERBOSE => true
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);

        curl_close($ch);

        $this->log("HTTP Code: " . $httpCode);
        $this->log("Response: " . $response);

        if ($error) {
            $this->log("CURL Error: " . $error);
            return false;
        }

        if ($httpCode !== 200) {
            $this->log("HTTP Error: " . $httpCode);
            return false;
        }

        return true;
    }

    public function testSymbolPrice($symbol = 'BTCUSDT') {
        $url = "https://api.binance.com/api/v3/ticker/price?symbol=" . $symbol;

        $this->log("Testing price fetch for symbol: " . $symbol);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; FolioFlow/1.0;)',
            CURLOPT_SSL_VERIFYPEER => true
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        $this->log("Response: " . $response);

        if ($error) {
            $this->log("CURL Error: " . $error);
            return false;
        }

        $data = json_decode($response, true);
        if (isset($data['price'])) {
            $this->log("Price received: " . $data['price']);
            return true;
        }

        $this->log("No price data in response");
        return false;
    }

    private function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message\n";
        error_log($logMessage, 3, $this->logFile);
    }
}