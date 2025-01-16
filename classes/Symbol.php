<?php
class Symbol {
    private $db;
    private const UPDATE_INTERVAL = 1800; // How often price updates are needed (30 minutes)
    private const BINANCE_API_ENDPOINT = "https://api.binance.com/api/v3";
    private $logFile;

    public function __construct($database) {
        $this->db = $database;
        $this->logFile = __DIR__ . '/../logs/symbol.log';
    }

    private function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message\n";
        error_log($logMessage, 3, $this->logFile);
    }

    public function addSymbol($symbol) {
        try {
            $this->log("Adding symbol: $symbol");

            // Check if symbol already exists
            $checkStmt = $this->db->prepare("
                SELECT id FROM symbols WHERE symbol = ?
            ");
            $checkStmt->execute([$symbol]);

            if (!$checkStmt->fetch()) {
                $stmt = $this->db->prepare("
                    INSERT INTO symbols (symbol, name, type) 
                    VALUES (?, ?, ?)
                ");

                $type = $this->isCryptoSymbolValid($symbol) ? 'crypto' : 'stock';
                $name = $type === 'crypto' ? str_replace('USDT', '', $symbol) : $symbol;

                $result = $stmt->execute([$symbol, $name, $type]);
                $this->log("Symbol added successfully: $symbol");
                return $result;
            }
            $this->log("Symbol already exists: $symbol");
            return true;
        } catch (Exception $e) {
            $this->log("Error adding symbol $symbol: " . $e->getMessage());
            return false;
        }
    }

    public function needsPriceUpdate($symbol) {
        try {
            $stmt = $this->db->prepare("
                SELECT last_updated 
                FROM symbols 
                WHERE symbol = ?
            ");
            $stmt->execute([$symbol]);
            $result = $stmt->fetch();

            // $result is false (symbol not found in the database) or last_updated is null or empty
            if (!$result || !$result['last_updated']) {
                return true;
            }

            $lastUpdate = strtotime($result['last_updated']);
            // Control is the update up to date based on our UPDATE_INTERVAL
            $needsUpdate = (time() - $lastUpdate) > self::UPDATE_INTERVAL;

            if ($needsUpdate) {
                $this->log("Price update needed for $symbol (last update: " . date('Y-m-d H:i:s', $lastUpdate) . ")");
            }

            return $needsUpdate;
        } catch (Exception $e) {
            $this->log("Error checking update need for $symbol: " . $e->getMessage());
            return true;
        }
    }

    public function updatePrice($symbol) {
        try {
            $this->log("Starting price update for $symbol");

            // Validate symbol format
            if (!preg_match('/^[A-Z0-9]+USDT$/', $symbol)) {
                $this->log("Invalid symbol format: $symbol");
                return false;
            }

            // First make sure symbol exists
            $checkStmt = $this->db->prepare("
                SELECT id, type FROM symbols WHERE symbol = ?
            ");
            $checkStmt->execute([$symbol]);
            $symbolData = $checkStmt->fetch();

            // If symbol doesn't exist firstly add it to database
            if (!$symbolData) {
                $this->addSymbol($symbol);
                $symbolData = ['type' => strpos($symbol, 'USDT') !== false ? 'crypto' : 'stock'];
            }

            // At the current I only provide real time price update on cryptos
            // Only proceed if it's a crypto symbol
            if (!$this->isCryptoSymbolValid($symbol)) {
                $this->log("Invalid or non-crypto symbol: $symbol. Skipping update.");
                return false;
            }

            // Build Binance API URL
            $url = self::BINANCE_API_ENDPOINT . "/ticker/price?symbol=" . $symbol;
            $this->log("Requesting URL: " . $url);

            // Add request headers
            $headers = [
                'User-Agent: Mozilla/5.0 (compatible; FolioFlow/1.0;)',
                'Accept: application/json'
            ];

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; FolioFlow/1.0;)'
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);

            $this->log("API Response Code: $httpCode");
            if ($response) {
                $this->log("API Response: $response");
            }

            curl_close($ch);

            if ($error) {
                $this->log("CURL Error: $error");
                return false;
            }

            if ($httpCode !== 200) {
                $this->log("HTTP Error: $httpCode for $symbol");
                return false;
            }

            $data = json_decode($response, true);

            if (isset($data['price'])) {
                $price = floatval($data['price']);
                $this->log("Price received for $symbol: $price");

                $stmt = $this->db->prepare("
                    UPDATE symbols 
                    SET current_price = ?,
                        last_updated = CURRENT_TIMESTAMP
                    WHERE symbol = ?
                ");

                $success = $stmt->execute([$price, $symbol]);

                if ($success) {
                    // Verify update
                    $verifyStmt = $this->db->prepare("
                        SELECT current_price, last_updated 
                        FROM symbols 
                        WHERE symbol = ?
                    ");
                    $verifyStmt->execute([$symbol]);
                    $result = $verifyStmt->fetch();

                    if ($result) {
                        $this->log("Price update verified for $symbol: " . print_r($result, true));
                    }
                }

                return $success;
            }

            $this->log("No valid price data in response for $symbol");
            return false;
        } catch (Exception $e) {
            $this->log("Error updating price for $symbol: " . $e->getMessage());
            return false;
        }
    }

    public function updatePrices($symbols, $forceUpdate = false) {
        try {
            $this->log("Starting batch price update for symbols: " . implode(', ', $symbols));

            // Response includes all symbols tracked by Binance
            $url = self::BINANCE_API_ENDPOINT . "/ticker/price";

            $headers = [
                'User-Agent: Mozilla/5.0 (compatible; FolioFlow/1.0;)', // Identifies the client making the request
                'Accept: application/json' // Specifies the expected response format as JSON
            ];

            // cURL PHP Library for sending HTTP requests
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url, // api endpoint
                CURLOPT_RETURNTRANSFER => true, // ensuring the response is returned as a string
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_SSL_VERIFYPEER => true  // verifying SSL certificate
            ]);

            $response = curl_exec($ch); // execution and response
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);

            $this->log("Batch update HTTP code: $httpCode");

            curl_close($ch);

            if ($error) {
                $this->log("Batch update CURL error: $error");
                return false;
            }

            if ($httpCode !== 200) {
                $this->log("Batch update failed with HTTP code: $httpCode");
                return false;
            }

            $allPrices = json_decode($response, true);
            if (!is_array($allPrices)) {
                $this->log("Invalid response from Binance API");
                return false;
            }

            // A key-value pair array where the symbol is the key and its price is the value.
            $priceMap = [];
            // Quickly look up a symbols which we need
            foreach ($allPrices as $price) {
                $priceMap[$price['symbol']] = $price['price'];
            }

            // Update database in batch
            // Starts a database transaction to group multiple updates into one atomic operation.
            //
            $this->db->beginTransaction();
            try {
                $stmt = $this->db->prepare("
                    UPDATE symbols 
                    SET current_price = ?,
                        last_updated = CURRENT_TIMESTAMP
                    WHERE symbol = ?
                ");

                $updatedAny = false;
                foreach ($symbols as $symbol) {
                    // Is there price of particular symbol and should forced or is it need to update
                    if (isset($priceMap[$symbol]) && ($forceUpdate || $this->needsPriceUpdate($symbol))) {
                        $stmt->execute([$priceMap[$symbol], $symbol]);
                        $this->log("Updated price for $symbol: " . $priceMap[$symbol]);
                        $updatedAny = true;
                    }
                }

                $this->db->commit();
                $this->log("Batch update completed successfully");
                return $updatedAny;
            } catch (Exception $e) {
                $this->db->rollBack();
                $this->log("Error in batch update transaction: " . $e->getMessage());
                throw $e;
            }
        } catch (Exception $e) {
            $this->log("Critical error in batch update: " . $e->getMessage());
            return false;
        }
    }

    public function testApiConnection($testSymbol = 'BTCUSDT') {
        try {
            $this->log("Testing Binance API connection with symbol: $testSymbol");

            $url = self::BINANCE_API_ENDPOINT . "/ticker/price?symbol=" . $testSymbol;
            $this->log("Test URL: " . $url);

            $headers = [
                'User-Agent: Mozilla/5.0 (compatible; FolioFlow/1.0;)',
                'Accept: application/json'
            ];

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_SSL_VERIFYPEER => true
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);

            $this->log("Test connection HTTP code: $httpCode");
            if ($response) {
                $this->log("Test connection response: $response");
            }

            curl_close($ch);

            if ($error) {
                $this->log("Test connection CURL error: $error");
                return [
                    'success' => false,
                    'error' => $error
                ];
            }

            if ($httpCode !== 200) {
                $this->log("Test connection HTTP error: $httpCode");
                return [
                    'success' => false,
                    'error' => "HTTP Error: $httpCode"
                ];
            }

            $data = json_decode($response, true);
            return [
                'success' => true,
                'data' => $data
            ];

        } catch (Exception $e) {
            $this->log("Test connection error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    // Can be implemented when user need to choose crypto
    public function getAvailableCryptos(): array {
        try {
            $stmt = $this->db->prepare("
                SELECT symbol, name 
                FROM symbols 
                WHERE type = 'crypto' AND current_price IS NOT NULL
                ORDER BY symbol ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            $this->log("Error fetching available cryptos: " . $e->getMessage());
            return [];
        }
    }

    public function isCryptoSymbolValid(string $symbol): bool {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM symbols 
                WHERE symbol = ? AND type = 'crypto'
            ");
            $stmt->execute([strtoupper($symbol)]);
            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (Exception $e) {
            $this->log("Error validating crypto symbol: " . $e->getMessage());
            return false;
        }
    }
}