<?php
/**
 * Database class responsible for establishing a secure and reusable PDO connection.
 */
class Database {
    /** @var PDO|null Active database connection */
    public ?PDO $connection = null;

    /** @var array PDO connection options */
    private array $options;

    /**
     * Initialize and establish the database connection..
     */
    public function __construct(array $config) {
        // Recommended PDO options for error handling and performance
        $this->options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,                 // Throw exceptions on errors
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,           // Fetch results as associative arrays
            PDO::ATTR_EMULATE_PREPARES => false,                        // Use native prepared statements
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci" // Full Unicode support
        ];

        try {
            // Construct the Data Source Name (DSN)
            $dsn = sprintf(
                "mysql:host=%s;port=%s;dbname=%s;charset=%s",
                $config['host'],
                $config['port'],
                $config['dbname'],
                $config['charset']
            );

            // Create a new PDO instance with the provided credentials and options
            $this->connection = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $this->options
            );
        } catch (PDOException $e) {
            // Log the actual error but return a generic message to the user
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed. Please try again later.");
        }
    }

    /**
     * Prepare a SQL query using PDO.
     */
    public function prepare($query): PDOStatement {
        try {
            return $this->connection->prepare($query);
        } catch (PDOException $e) {
            error_log("Query preparation failed: " . $e->getMessage());
            throw new Exception("Database error occurred. Please try again later.");
        }
    }

    /**
     * Begin a database transaction.
     */
    public function beginTransaction(): bool {
        return $this->connection->beginTransaction();
    }

    /**
     * Commit the current transaction.
     */
    public function commit(): bool {
        return $this->connection->commit();
    }

    /**
     * Roll back the current transaction.
     */
    public function rollBack(): bool {
        return $this->connection->rollBack();
    }

    /**
     * Get the ID of the last inserted row.
     */
    public function lastInsertId(): string {
        return $this->connection->lastInsertId();
    }
}