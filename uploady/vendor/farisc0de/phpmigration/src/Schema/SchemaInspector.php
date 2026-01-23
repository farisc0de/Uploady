<?php

namespace Farisc0de\PhpMigration\Schema;

use Farisc0de\PhpMigration\Contracts\ConnectionInterface;

/**
 * Class SchemaInspector
 * 
 * Provides schema introspection capabilities
 */
class SchemaInspector
{
    /**
     * The database connection
     *
     * @var ConnectionInterface
     */
    protected ConnectionInterface $connection;

    /**
     * Create a new schema inspector instance
     *
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get all tables in the database
     *
     * @return array
     */
    public function getTables(): array
    {
        $driver = $this->connection->getDriverName();
        $database = $this->connection->getDatabaseName();

        $sql = match ($driver) {
            'mysql' => "SELECT table_name FROM information_schema.tables WHERE table_schema = :database AND table_type = 'BASE TABLE'",
            'pgsql' => "SELECT tablename as table_name FROM pg_tables WHERE schemaname = 'public'",
            'sqlite' => "SELECT name as table_name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%'",
            default => throw new \RuntimeException("Unsupported driver: {$driver}"),
        };

        $this->connection->prepare($sql);
        
        if ($driver === 'mysql') {
            $this->connection->bind(':database', $database);
        }
        
        $this->connection->execute();
        $results = $this->connection->resultset();

        return array_map(function ($row) {
            return is_object($row) ? $row->table_name : $row['table_name'];
        }, $results);
    }

    /**
     * Check if a table exists
     *
     * @param string $table
     * @return bool
     */
    public function hasTable(string $table): bool
    {
        return in_array($table, $this->getTables());
    }

    /**
     * Get all columns for a table
     *
     * @param string $table
     * @return array
     */
    public function getColumns(string $table): array
    {
        $driver = $this->connection->getDriverName();
        $database = $this->connection->getDatabaseName();

        $sql = match ($driver) {
            'mysql' => "SELECT 
                column_name, 
                data_type, 
                column_type,
                is_nullable, 
                column_default, 
                column_key,
                extra,
                column_comment
            FROM information_schema.columns 
            WHERE table_schema = :database AND table_name = :table
            ORDER BY ordinal_position",
            
            'pgsql' => "SELECT 
                column_name, 
                data_type, 
                udt_name as column_type,
                is_nullable, 
                column_default,
                '' as column_key,
                '' as extra,
                '' as column_comment
            FROM information_schema.columns 
            WHERE table_catalog = :database AND table_name = :table
            ORDER BY ordinal_position",
            
            'sqlite' => "PRAGMA table_info({$table})",
            
            default => throw new \RuntimeException("Unsupported driver: {$driver}"),
        };

        if ($driver === 'sqlite') {
            $this->connection->prepare($sql);
            $this->connection->execute();
            $results = $this->connection->resultset();

            return array_map(function ($row) {
                $row = is_object($row) ? (array) $row : $row;
                return [
                    'name' => $row['name'],
                    'type' => $row['type'],
                    'nullable' => !$row['notnull'],
                    'default' => $row['dflt_value'],
                    'primary' => (bool) $row['pk'],
                ];
            }, $results);
        }

        $this->connection->prepare($sql);
        $this->connection->bind(':database', $database);
        $this->connection->bind(':table', $table);
        $this->connection->execute();
        $results = $this->connection->resultset();

        return array_map(function ($row) {
            $row = is_object($row) ? (array) $row : $row;
            return [
                'name' => $row['column_name'],
                'type' => $row['data_type'],
                'full_type' => $row['column_type'],
                'nullable' => $row['is_nullable'] === 'YES',
                'default' => $row['column_default'],
                'key' => $row['column_key'] ?? null,
                'extra' => $row['extra'] ?? null,
                'comment' => $row['column_comment'] ?? null,
            ];
        }, $results);
    }

    /**
     * Check if a column exists in a table
     *
     * @param string $table
     * @param string $column
     * @return bool
     */
    public function hasColumn(string $table, string $column): bool
    {
        $columns = $this->getColumns($table);
        
        foreach ($columns as $col) {
            if ($col['name'] === $column) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get column type
     *
     * @param string $table
     * @param string $column
     * @return string|null
     */
    public function getColumnType(string $table, string $column): ?string
    {
        $columns = $this->getColumns($table);
        
        foreach ($columns as $col) {
            if ($col['name'] === $column) {
                return $col['type'];
            }
        }
        
        return null;
    }

    /**
     * Get all indexes for a table
     *
     * @param string $table
     * @return array
     */
    public function getIndexes(string $table): array
    {
        $driver = $this->connection->getDriverName();
        $database = $this->connection->getDatabaseName();

        $sql = match ($driver) {
            'mysql' => "SHOW INDEX FROM {$table}",
            'pgsql' => "SELECT 
                i.relname as index_name,
                a.attname as column_name,
                ix.indisunique as is_unique,
                ix.indisprimary as is_primary
            FROM pg_class t
            JOIN pg_index ix ON t.oid = ix.indrelid
            JOIN pg_class i ON i.oid = ix.indexrelid
            JOIN pg_attribute a ON a.attrelid = t.oid AND a.attnum = ANY(ix.indkey)
            WHERE t.relname = :table",
            'sqlite' => "PRAGMA index_list({$table})",
            default => throw new \RuntimeException("Unsupported driver: {$driver}"),
        };

        $this->connection->prepare($sql);
        
        if ($driver === 'pgsql') {
            $this->connection->bind(':table', $table);
        }
        
        $this->connection->execute();
        $results = $this->connection->resultset();

        if ($driver === 'mysql') {
            $indexes = [];
            foreach ($results as $row) {
                $row = is_object($row) ? (array) $row : $row;
                $name = $row['Key_name'];
                if (!isset($indexes[$name])) {
                    $indexes[$name] = [
                        'name' => $name,
                        'columns' => [],
                        'unique' => !$row['Non_unique'],
                        'primary' => $name === 'PRIMARY',
                    ];
                }
                $indexes[$name]['columns'][] = $row['Column_name'];
            }
            return array_values($indexes);
        }

        if ($driver === 'sqlite') {
            $indexes = [];
            foreach ($results as $row) {
                $row = is_object($row) ? (array) $row : $row;
                $indexName = $row['name'];
                
                // Get columns for this index
                $this->connection->prepare("PRAGMA index_info({$indexName})");
                $this->connection->execute();
                $indexInfo = $this->connection->resultset();
                
                $columns = array_map(function ($info) {
                    $info = is_object($info) ? (array) $info : $info;
                    return $info['name'];
                }, $indexInfo);
                
                $indexes[] = [
                    'name' => $indexName,
                    'columns' => $columns,
                    'unique' => (bool) $row['unique'],
                    'primary' => false,
                ];
            }
            return $indexes;
        }

        // PostgreSQL
        $indexes = [];
        foreach ($results as $row) {
            $row = is_object($row) ? (array) $row : $row;
            $name = $row['index_name'];
            if (!isset($indexes[$name])) {
                $indexes[$name] = [
                    'name' => $name,
                    'columns' => [],
                    'unique' => (bool) $row['is_unique'],
                    'primary' => (bool) $row['is_primary'],
                ];
            }
            $indexes[$name]['columns'][] = $row['column_name'];
        }
        return array_values($indexes);
    }

    /**
     * Get all foreign keys for a table
     *
     * @param string $table
     * @return array
     */
    public function getForeignKeys(string $table): array
    {
        $driver = $this->connection->getDriverName();
        $database = $this->connection->getDatabaseName();

        $sql = match ($driver) {
            'mysql' => "SELECT 
                constraint_name,
                column_name,
                referenced_table_name,
                referenced_column_name
            FROM information_schema.key_column_usage
            WHERE table_schema = :database 
                AND table_name = :table 
                AND referenced_table_name IS NOT NULL",
            
            'pgsql' => "SELECT
                tc.constraint_name,
                kcu.column_name,
                ccu.table_name AS referenced_table_name,
                ccu.column_name AS referenced_column_name
            FROM information_schema.table_constraints AS tc
            JOIN information_schema.key_column_usage AS kcu
                ON tc.constraint_name = kcu.constraint_name
            JOIN information_schema.constraint_column_usage AS ccu
                ON ccu.constraint_name = tc.constraint_name
            WHERE tc.constraint_type = 'FOREIGN KEY' 
                AND tc.table_name = :table",
            
            'sqlite' => "PRAGMA foreign_key_list({$table})",
            
            default => throw new \RuntimeException("Unsupported driver: {$driver}"),
        };

        $this->connection->prepare($sql);
        
        if ($driver === 'mysql') {
            $this->connection->bind(':database', $database);
            $this->connection->bind(':table', $table);
        } elseif ($driver === 'pgsql') {
            $this->connection->bind(':table', $table);
        }
        
        $this->connection->execute();
        $results = $this->connection->resultset();

        if ($driver === 'sqlite') {
            return array_map(function ($row) {
                $row = is_object($row) ? (array) $row : $row;
                return [
                    'name' => "fk_{$row['from']}",
                    'column' => $row['from'],
                    'referenced_table' => $row['table'],
                    'referenced_column' => $row['to'],
                    'on_update' => $row['on_update'],
                    'on_delete' => $row['on_delete'],
                ];
            }, $results);
        }

        return array_map(function ($row) {
            $row = is_object($row) ? (array) $row : $row;
            return [
                'name' => $row['constraint_name'],
                'column' => $row['column_name'],
                'referenced_table' => $row['referenced_table_name'],
                'referenced_column' => $row['referenced_column_name'],
            ];
        }, $results);
    }

    /**
     * Get the primary key columns for a table
     *
     * @param string $table
     * @return array
     */
    public function getPrimaryKey(string $table): array
    {
        $indexes = $this->getIndexes($table);
        
        foreach ($indexes as $index) {
            if ($index['primary'] ?? false) {
                return $index['columns'];
            }
        }
        
        // For SQLite, check columns directly
        if ($this->connection->getDriverName() === 'sqlite') {
            $columns = $this->getColumns($table);
            $primary = [];
            foreach ($columns as $col) {
                if ($col['primary'] ?? false) {
                    $primary[] = $col['name'];
                }
            }
            return $primary;
        }
        
        return [];
    }

    /**
     * Get table details
     *
     * @param string $table
     * @return array
     */
    public function getTableDetails(string $table): array
    {
        return [
            'name' => $table,
            'columns' => $this->getColumns($table),
            'indexes' => $this->getIndexes($table),
            'foreign_keys' => $this->getForeignKeys($table),
            'primary_key' => $this->getPrimaryKey($table),
        ];
    }

    /**
     * Get the database size in bytes
     *
     * @return int
     */
    public function getDatabaseSize(): int
    {
        $driver = $this->connection->getDriverName();
        $database = $this->connection->getDatabaseName();

        $sql = match ($driver) {
            'mysql' => "SELECT SUM(data_length + index_length) as size 
                FROM information_schema.tables 
                WHERE table_schema = :database",
            'pgsql' => "SELECT pg_database_size(:database) as size",
            'sqlite' => "SELECT page_count * page_size as size FROM pragma_page_count(), pragma_page_size()",
            default => throw new \RuntimeException("Unsupported driver: {$driver}"),
        };

        $this->connection->prepare($sql);
        
        if ($driver !== 'sqlite') {
            $this->connection->bind(':database', $database);
        }
        
        $this->connection->execute();
        $result = $this->connection->single();
        
        $result = is_object($result) ? (array) $result : $result;
        
        return (int) ($result['size'] ?? 0);
    }

    /**
     * Get table row count
     *
     * @param string $table
     * @return int
     */
    public function getTableRowCount(string $table): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$table}";
        
        $this->connection->prepare($sql);
        $this->connection->execute();
        $result = $this->connection->single();
        
        $result = is_object($result) ? (array) $result : $result;
        
        return (int) ($result['count'] ?? 0);
    }
}
