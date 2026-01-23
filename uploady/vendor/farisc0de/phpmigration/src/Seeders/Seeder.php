<?php

namespace Farisc0de\PhpMigration\Seeders;

use Farisc0de\PhpMigration\Contracts\ConnectionInterface;
use Farisc0de\PhpMigration\Contracts\SeederInterface;

/**
 * Class Seeder
 * 
 * Base seeder class for database seeding
 */
abstract class Seeder implements SeederInterface
{
    /**
     * The database connection
     *
     * @var ConnectionInterface
     */
    protected ConnectionInterface $connection;

    /**
     * The seeder manager
     *
     * @var SeederManager|null
     */
    protected ?SeederManager $manager = null;

    /**
     * Set the database connection
     *
     * @param ConnectionInterface $connection
     * @return $this
     */
    public function setConnection(ConnectionInterface $connection): self
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * Set the seeder manager
     *
     * @param SeederManager $manager
     * @return $this
     */
    public function setManager(SeederManager $manager): self
    {
        $this->manager = $manager;
        return $this;
    }

    /**
     * Run the database seeds
     *
     * @return void
     */
    abstract public function run(): void;

    /**
     * Call another seeder
     *
     * @param string $class
     * @return void
     */
    public function call(string $class): void
    {
        if ($this->manager) {
            $this->manager->call($class);
        } else {
            $seeder = new $class();
            $seeder->setConnection($this->connection);
            $seeder->run();
        }
    }

    /**
     * Call multiple seeders
     *
     * @param array $classes
     * @return void
     */
    public function callMany(array $classes): void
    {
        foreach ($classes as $class) {
            $this->call($class);
        }
    }

    /**
     * Insert data into a table
     *
     * @param string $table
     * @param array $data
     * @return bool
     */
    protected function insert(string $table, array $data): bool
    {
        if (empty($data)) {
            return false;
        }

        // Check if it's a single record or multiple records
        $isMultiple = isset($data[0]) && is_array($data[0]);

        if (!$isMultiple) {
            $data = [$data];
        }

        foreach ($data as $record) {
            $columns = array_keys($record);
            $placeholders = array_map(fn($col) => ":{$col}", $columns);

            $sql = sprintf(
                "INSERT INTO %s (%s) VALUES (%s)",
                $table,
                implode(', ', $columns),
                implode(', ', $placeholders)
            );

            $this->connection->prepare($sql);

            foreach ($record as $column => $value) {
                $this->connection->bind(":{$column}", $value);
            }

            $this->connection->execute();
        }

        return true;
    }

    /**
     * Truncate a table
     *
     * @param string $table
     * @return void
     */
    protected function truncate(string $table): void
    {
        $driverName = $this->connection->getDriverName();

        $sql = match ($driverName) {
            'mysql' => "TRUNCATE TABLE {$table}",
            'pgsql' => "TRUNCATE TABLE {$table} RESTART IDENTITY CASCADE",
            'sqlite' => "DELETE FROM {$table}",
            default => "DELETE FROM {$table}",
        };

        $this->connection->exec($sql);

        // Reset auto-increment for SQLite
        if ($driverName === 'sqlite') {
            $this->connection->exec("DELETE FROM sqlite_sequence WHERE name = '{$table}'");
        }
    }

    /**
     * Disable foreign key checks
     *
     * @return void
     */
    protected function disableForeignKeyChecks(): void
    {
        $driverName = $this->connection->getDriverName();

        $sql = match ($driverName) {
            'mysql' => 'SET FOREIGN_KEY_CHECKS=0',
            'pgsql' => 'SET CONSTRAINTS ALL DEFERRED',
            'sqlite' => 'PRAGMA foreign_keys = OFF',
            default => null,
        };

        if ($sql) {
            $this->connection->exec($sql);
        }
    }

    /**
     * Enable foreign key checks
     *
     * @return void
     */
    protected function enableForeignKeyChecks(): void
    {
        $driverName = $this->connection->getDriverName();

        $sql = match ($driverName) {
            'mysql' => 'SET FOREIGN_KEY_CHECKS=1',
            'pgsql' => 'SET CONSTRAINTS ALL IMMEDIATE',
            'sqlite' => 'PRAGMA foreign_keys = ON',
            default => null,
        };

        if ($sql) {
            $this->connection->exec($sql);
        }
    }
}
