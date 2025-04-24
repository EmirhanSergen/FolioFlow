<?php
/**
 * Establish and return a database connection using project config.
 */
// Establishes a connection to the database
function connectDB() {
    // Load database configuration from the config file
    $config = require __DIR__ . '/config.php';

    // Ensure database configuration exists
    if (!isset($config['database'])) {
        throw new Exception("Database configuration is missing in config.php.");
    }

    // Return a new DataBase object initialized with the config parameters
    try {
        return new DataBase($config['database']); // Create and return the database connection
    } catch (Exception $e) {
        // Log the error and provide meaningful feedback
        error_log("Database connection failed: " . $e->getMessage());
        throw new Exception("Unable to connect to the database.");
    }
}