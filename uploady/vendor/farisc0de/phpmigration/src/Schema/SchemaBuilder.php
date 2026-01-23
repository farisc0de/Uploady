<?php

namespace Farisc0de\PhpMigration\Schema;

use Closure;
use Farisc0de\PhpMigration\Contracts\ConnectionInterface;
use Farisc0de\PhpMigration\Contracts\SchemaGrammarInterface;

/**
 * Class SchemaBuilder
 * 
 * Provides a fluent interface for database schema operations
 */
class SchemaBuilder
{
    /**
     * The database connection
     *
     * @var ConnectionInterface
     */
    protected ConnectionInterface $connection;

    /**
     * The schema grammar
     *
     * @var SchemaGrammarInterface
     */
    protected SchemaGrammarInterface $grammar;

    /**
     * The default string length for migrations
     *
     * @var int
     */
    public static int $defaultStringLength = 255;

    /**
     * The default morph key type
     *
     * @var string
     */
    public static string $defaultMorphKeyType = 'int';

    /**
     * Create a new schema builder instance
     *
     * @param ConnectionInterface $connection
     * @param SchemaGrammarInterface $grammar
     */
    public function __construct(ConnectionInterface $connection, SchemaGrammarInterface $grammar)
    {
        $this->connection = $connection;
        $this->grammar = $grammar;
    }

    /**
     * Set the default string length for migrations
     *
     * @param int $length
     * @return void
     */
    public static function defaultStringLength(int $length): void
    {
        static::$defaultStringLength = $length;
    }

    /**
     * Set the default morph key type for migrations
     *
     * @param string $type
     * @return void
     */
    public static function defaultMorphKeyType(string $type): void
    {
        static::$defaultMorphKeyType = $type;
    }

    /**
     * Create a new table on the schema
     *
     * @param string $table
     * @param Closure $callback
     * @return void
     */
    public function create(string $table, Closure $callback): void
    {
        $blueprint = $this->createBlueprint($table);

        $callback($blueprint);

        $this->build($blueprint, 'create');
    }

    /**
     * Modify a table on the schema
     *
     * @param string $table
     * @param Closure $callback
     * @return void
     */
    public function table(string $table, Closure $callback): void
    {
        $blueprint = $this->createBlueprint($table);

        $callback($blueprint);

        $this->build($blueprint, 'alter');
    }

    /**
     * Drop a table from the schema
     *
     * @param string $table
     * @return void
     */
    public function drop(string $table): void
    {
        $sql = $this->grammar->compileDrop($table);
        $this->connection->exec($sql);
    }

    /**
     * Drop a table from the schema if it exists
     *
     * @param string $table
     * @return void
     */
    public function dropIfExists(string $table): void
    {
        $sql = $this->grammar->compileDropIfExists($table);
        $this->connection->exec($sql);
    }

    /**
     * Drop all tables from the database
     *
     * @return void
     */
    public function dropAllTables(): void
    {
        $tables = $this->getAllTables();

        if (empty($tables)) {
            return;
        }

        $this->disableForeignKeyConstraints();

        foreach ($tables as $table) {
            $this->drop($table);
        }

        $this->enableForeignKeyConstraints();
    }

    /**
     * Rename a table on the schema
     *
     * @param string $from
     * @param string $to
     * @return void
     */
    public function rename(string $from, string $to): void
    {
        $sql = $this->grammar->compileRename($from, $to);
        $this->connection->exec($sql);
    }

    /**
     * Determine if the given table exists
     *
     * @param string $table
     * @return bool
     */
    public function hasTable(string $table): bool
    {
        $sql = $this->grammar->compileTableExists();
        
        $this->connection->prepare($sql);
        $this->connection->bind(':table', $table);
        $this->connection->bind(':database', $this->connection->getDatabaseName());
        $this->connection->execute();

        return $this->connection->rowCount() > 0;
    }

    /**
     * Determine if the given table has a given column
     *
     * @param string $table
     * @param string $column
     * @return bool
     */
    public function hasColumn(string $table, string $column): bool
    {
        return in_array(
            strtolower($column),
            array_map('strtolower', $this->getColumnListing($table))
        );
    }

    /**
     * Determine if the given table has given columns
     *
     * @param string $table
     * @param array $columns
     * @return bool
     */
    public function hasColumns(string $table, array $columns): bool
    {
        $tableColumns = array_map('strtolower', $this->getColumnListing($table));

        foreach ($columns as $column) {
            if (!in_array(strtolower($column), $tableColumns)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the column listing for a given table
     *
     * @param string $table
     * @return array
     */
    public function getColumnListing(string $table): array
    {
        $sql = $this->grammar->compileColumnListing();

        $this->connection->prepare($sql);
        $this->connection->bind(':table', $table);
        $this->connection->bind(':database', $this->connection->getDatabaseName());
        $this->connection->execute();

        $results = $this->connection->resultset();

        return array_map(function ($result) {
            return is_object($result) ? $result->column_name : $result['column_name'];
        }, $results);
    }

    /**
     * Get all tables from the database
     *
     * @return array
     */
    public function getAllTables(): array
    {
        $driverName = $this->connection->getDriverName();
        
        switch ($driverName) {
            case 'mysql':
                $sql = 'SHOW TABLES';
                break;
            case 'pgsql':
                $sql = "SELECT tablename FROM pg_tables WHERE schemaname = 'public'";
                break;
            case 'sqlite':
                $sql = "SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'";
                break;
            default:
                return [];
        }

        $this->connection->prepare($sql);
        $this->connection->execute();

        $results = $this->connection->resultset();

        return array_map(function ($result) {
            if (is_object($result)) {
                return reset((array)$result);
            }
            return reset($result);
        }, $results);
    }

    /**
     * Enable foreign key constraints
     *
     * @return bool
     */
    public function enableForeignKeyConstraints(): bool
    {
        $driverName = $this->connection->getDriverName();

        $sql = match ($driverName) {
            'mysql' => 'SET FOREIGN_KEY_CHECKS=1',
            'pgsql' => 'SET CONSTRAINTS ALL IMMEDIATE',
            'sqlite' => 'PRAGMA foreign_keys = ON',
            default => null,
        };

        if ($sql === null) {
            return false;
        }

        return $this->connection->exec($sql) !== false;
    }

    /**
     * Disable foreign key constraints
     *
     * @return bool
     */
    public function disableForeignKeyConstraints(): bool
    {
        $driverName = $this->connection->getDriverName();

        $sql = match ($driverName) {
            'mysql' => 'SET FOREIGN_KEY_CHECKS=0',
            'pgsql' => 'SET CONSTRAINTS ALL DEFERRED',
            'sqlite' => 'PRAGMA foreign_keys = OFF',
            default => null,
        };

        if ($sql === null) {
            return false;
        }

        return $this->connection->exec($sql) !== false;
    }

    /**
     * Create a new blueprint instance
     *
     * @param string $table
     * @return Blueprint
     */
    protected function createBlueprint(string $table): Blueprint
    {
        return new Blueprint($table);
    }

    /**
     * Execute the blueprint to build the schema
     *
     * @param Blueprint $blueprint
     * @param string $type
     * @return void
     */
    protected function build(Blueprint $blueprint, string $type): void
    {
        $statements = match ($type) {
            'create' => $this->grammar->compileCreate($blueprint),
            'alter' => array_merge(
                $this->grammar->compileAdd($blueprint),
                $this->grammar->compileModify($blueprint)
            ),
            default => [],
        };

        foreach ($statements as $statement) {
            $this->connection->exec($statement);
        }
    }

    /**
     * Get the database connection
     *
     * @return ConnectionInterface
     */
    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }

    /**
     * Get the schema grammar
     *
     * @return SchemaGrammarInterface
     */
    public function getGrammar(): SchemaGrammarInterface
    {
        return $this->grammar;
    }
}
