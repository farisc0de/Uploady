<?php

namespace Farisc0de\PhpMigration\Contracts;

/**
 * Interface ConnectionInterface
 * 
 * Defines the contract for database connections
 */
interface ConnectionInterface
{
    /**
     * Prepare a SQL statement
     *
     * @param string $query The SQL query
     * @return void
     */
    public function prepare(string $query): void;

    /**
     * Execute the prepared statement
     *
     * @return bool
     */
    public function execute(): bool;

    /**
     * Execute a query without results
     *
     * @param string $query The SQL query
     * @return int|false
     */
    public function exec(string $query): int|false;

    /**
     * Bind a value to a parameter
     *
     * @param string $param The parameter name
     * @param mixed $value The value to bind
     * @param int|null $type The PDO type
     * @return void
     */
    public function bind(string $param, mixed $value, ?int $type = null): void;

    /**
     * Get all results as an array
     *
     * @return array
     */
    public function resultset(): array;

    /**
     * Get a single result
     *
     * @return object|array|false
     */
    public function single(): object|array|false;

    /**
     * Get the row count
     *
     * @return int
     */
    public function rowCount(): int;

    /**
     * Get the last inserted ID
     *
     * @return string|false
     */
    public function lastInsertId(): string|false;

    /**
     * Begin a transaction
     *
     * @return bool
     */
    public function beginTransaction(): bool;

    /**
     * Commit a transaction
     *
     * @return bool
     */
    public function commit(): bool;

    /**
     * Rollback a transaction
     *
     * @return bool
     */
    public function rollback(): bool;

    /**
     * Check if connected
     *
     * @return bool
     */
    public function isConnected(): bool;

    /**
     * Get the database name
     *
     * @return string
     */
    public function getDatabaseName(): string;

    /**
     * Get the driver name
     *
     * @return string
     */
    public function getDriverName(): string;
}
