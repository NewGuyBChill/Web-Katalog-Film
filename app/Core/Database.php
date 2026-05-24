<?php

namespace App\Core;

use PDO;
use PDOException;
use PDOStatement;

/**
 * Database — PDO Wrapper with Singleton Pattern
 * 
 * Provides a clean API for database operations:
 * - Prepared statements
 * - Query builder shortcuts
 * - Transaction support
 */
class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $host     = env('DB_HOST', 'localhost');
        $port     = env('DB_PORT', '3306');
        $database = env('DB_DATABASE', 'kinema_db');
        $username = env('DB_USERNAME', 'root');
        $password = env('DB_PASSWORD', '');

        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

        try {
            $this->pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            ]);
        } catch (PDOException $e) {
            if (env('APP_DEBUG', false)) {
                die("Database connection failed: " . $e->getMessage());
            }
            die("Database connection failed. Please check your configuration.");
        }
    }

    /**
     * Singleton: returns the single Database instance.
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get the underlying PDO connection.
     */
    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    /**
     * Execute a prepared query and return the PDOStatement.
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch all rows from a query.
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Fetch a single row from a query.
     */
    public function fetch(string $sql, array $params = []): ?array
    {
        $result = $this->query($sql, $params)->fetch();
        return $result ?: null;
    }

    /**
     * Fetch a single column value from a query.
     */
    public function fetchColumn(string $sql, array $params = []): mixed
    {
        return $this->query($sql, $params)->fetchColumn();
    }

    /**
     * Insert a row and return the last insert ID.
     */
    public function insert(string $table, array $data): string|false
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, array_values($data));

        return $this->pdo->lastInsertId();
    }

    /**
     * Update rows matching conditions.
     */
    public function update(string $table, array $data, array $where): int
    {
        $setClauses = [];
        $values = [];

        foreach ($data as $column => $value) {
            $setClauses[] = "{$column} = ?";
            $values[] = $value;
        }

        $whereClauses = [];
        foreach ($where as $column => $value) {
            $whereClauses[] = "{$column} = ?";
            $values[] = $value;
        }

        $sql = "UPDATE {$table} SET " . implode(', ', $setClauses)
             . " WHERE " . implode(' AND ', $whereClauses);

        return $this->query($sql, $values)->rowCount();
    }

    /**
     * Delete rows matching conditions.
     */
    public function delete(string $table, array $where): int
    {
        $whereClauses = [];
        $values = [];

        foreach ($where as $column => $value) {
            $whereClauses[] = "{$column} = ?";
            $values[] = $value;
        }

        $sql = "DELETE FROM {$table} WHERE " . implode(' AND ', $whereClauses);

        return $this->query($sql, $values)->rowCount();
    }

    /**
     * Begin a database transaction.
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit the current transaction.
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    /**
     * Roll back the current transaction.
     */
    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }
}
