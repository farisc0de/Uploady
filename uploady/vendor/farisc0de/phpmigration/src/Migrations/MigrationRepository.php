<?php

namespace Farisc0de\PhpMigration\Migrations;

use Farisc0de\PhpMigration\Contracts\ConnectionInterface;

/**
 * Class MigrationRepository
 * 
 * Manages the migrations table that tracks which migrations have been run
 */
class MigrationRepository
{
    /**
     * The database connection
     *
     * @var ConnectionInterface
     */
    protected ConnectionInterface $connection;

    /**
     * The name of the migrations table
     *
     * @var string
     */
    protected string $table;

    /**
     * Create a new migration repository instance
     *
     * @param ConnectionInterface $connection
     * @param string $table
     */
    public function __construct(ConnectionInterface $connection, string $table = 'migrations')
    {
        $this->connection = $connection;
        $this->table = $table;
    }

    /**
     * Get the completed migrations
     *
     * @return array
     */
    public function getRan(): array
    {
        $sql = "SELECT migration FROM {$this->table} ORDER BY batch, migration";
        
        $this->connection->prepare($sql);
        $this->connection->execute();
        
        $results = $this->connection->resultset();
        
        return array_map(function ($result) {
            return is_object($result) ? $result->migration : $result['migration'];
        }, $results);
    }

    /**
     * Get list of migrations for a batch
     *
     * @param int $batch
     * @return array
     */
    public function getMigrationsByBatch(int $batch): array
    {
        $sql = "SELECT migration FROM {$this->table} WHERE batch = :batch ORDER BY migration DESC";
        
        $this->connection->prepare($sql);
        $this->connection->bind(':batch', $batch);
        $this->connection->execute();
        
        $results = $this->connection->resultset();
        
        return array_map(function ($result) {
            return is_object($result) ? $result->migration : $result['migration'];
        }, $results);
    }

    /**
     * Get the last migration batch number
     *
     * @return int
     */
    public function getLastBatchNumber(): int
    {
        $sql = "SELECT MAX(batch) as batch FROM {$this->table}";
        
        $this->connection->prepare($sql);
        $this->connection->execute();
        
        $result = $this->connection->single();
        
        if ($result === false) {
            return 0;
        }
        
        $batch = is_object($result) ? $result->batch : ($result['batch'] ?? 0);
        
        return (int) ($batch ?? 0);
    }

    /**
     * Get the next migration batch number
     *
     * @return int
     */
    public function getNextBatchNumber(): int
    {
        return $this->getLastBatchNumber() + 1;
    }

    /**
     * Get the last migrations
     *
     * @param int $steps
     * @return array
     */
    public function getLast(int $steps = 1): array
    {
        $lastBatch = $this->getLastBatchNumber();
        
        if ($steps === 1) {
            return $this->getMigrationsByBatch($lastBatch);
        }

        $migrations = [];
        for ($i = 0; $i < $steps && $lastBatch - $i > 0; $i++) {
            $batchMigrations = $this->getMigrationsByBatch($lastBatch - $i);
            $migrations = array_merge($migrations, $batchMigrations);
        }

        return $migrations;
    }

    /**
     * Log that a migration was run
     *
     * @param string $file
     * @param int $batch
     * @return void
     */
    public function log(string $file, int $batch): void
    {
        $sql = "INSERT INTO {$this->table} (migration, batch) VALUES (:migration, :batch)";
        
        $this->connection->prepare($sql);
        $this->connection->bind(':migration', $file);
        $this->connection->bind(':batch', $batch);
        $this->connection->execute();
    }

    /**
     * Remove a migration from the log
     *
     * @param string $migration
     * @return void
     */
    public function delete(string $migration): void
    {
        $sql = "DELETE FROM {$this->table} WHERE migration = :migration";
        
        $this->connection->prepare($sql);
        $this->connection->bind(':migration', $migration);
        $this->connection->execute();
    }

    /**
     * Create the migration repository data store
     *
     * @return void
     */
    public function createRepository(): void
    {
        $driverName = $this->connection->getDriverName();

        $sql = match ($driverName) {
            'mysql' => "CREATE TABLE IF NOT EXISTS {$this->table} (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                batch INT NOT NULL,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            
            'pgsql' => "CREATE TABLE IF NOT EXISTS {$this->table} (
                id SERIAL PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                batch INTEGER NOT NULL,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            
            'sqlite' => "CREATE TABLE IF NOT EXISTS {$this->table} (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                migration TEXT NOT NULL,
                batch INTEGER NOT NULL,
                executed_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )",
            
            default => throw new \RuntimeException("Unsupported database driver: {$driverName}"),
        };

        $this->connection->exec($sql);
    }

    /**
     * Determine if the migration repository exists
     *
     * @return bool
     */
    public function repositoryExists(): bool
    {
        $driverName = $this->connection->getDriverName();

        $sql = match ($driverName) {
            'mysql' => "SELECT * FROM information_schema.tables WHERE table_schema = :database AND table_name = :table",
            'pgsql' => "SELECT * FROM information_schema.tables WHERE table_catalog = :database AND table_name = :table",
            'sqlite' => "SELECT * FROM sqlite_master WHERE type = 'table' AND name = :table",
            default => throw new \RuntimeException("Unsupported database driver: {$driverName}"),
        };

        $this->connection->prepare($sql);
        
        if ($driverName !== 'sqlite') {
            $this->connection->bind(':database', $this->connection->getDatabaseName());
        }
        
        $this->connection->bind(':table', $this->table);
        $this->connection->execute();

        return $this->connection->rowCount() > 0;
    }

    /**
     * Delete the migration repository data store
     *
     * @return void
     */
    public function deleteRepository(): void
    {
        $sql = "DROP TABLE IF EXISTS {$this->table}";
        $this->connection->exec($sql);
    }

    /**
     * Get the migration table name
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Set the migration table name
     *
     * @param string $table
     * @return void
     */
    public function setTable(string $table): void
    {
        $this->table = $table;
    }
}
