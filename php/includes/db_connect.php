<?php
// php/includes/db_connect.php
require_once __DIR__ . '/../config.php'; // Include constants

/**
 * Establishes a PDO database connection.
 *
 * @return PDO|null Returns a PDO connection object on success, null on failure.
 */
function getDbConnection(): ?PDO {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (\PDOException $e) {
        // In production, log the error instead of echoing
        error_log("Database Connection Error: " . $e->getMessage());
        // Optionally: throw new \PDOException($e->getMessage(), (int)$e->getCode());
        return null; // Indicate connection failure
    }
}
?>