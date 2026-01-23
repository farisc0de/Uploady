<?php

namespace Farisc0de\PhpMigration\Schema\Grammars;

use Farisc0de\PhpMigration\Schema\Blueprint;
use Farisc0de\PhpMigration\Schema\ColumnDefinition;

/**
 * Class SqliteGrammar
 * 
 * SQLite-specific SQL grammar
 */
class SqliteGrammar extends Grammar
{
    /**
     * The column modifiers
     *
     * @var array
     */
    protected array $modifiers = [
        'Nullable', 'Default', 'Increment'
    ];

    /**
     * Get the driver name
     *
     * @return string
     */
    public function getDriverName(): string
    {
        return 'sqlite';
    }

    /**
     * Wrap a value in keyword identifiers
     *
     * @param string $value
     * @return string
     */
    public function wrap(string $value): string
    {
        if ($value === '*') {
            return $value;
        }

        return '"' . str_replace('"', '""', $value) . '"';
    }

    /**
     * Compile a create table command
     *
     * @param Blueprint $blueprint
     * @return array
     */
    public function compileCreate(Blueprint $blueprint): array
    {
        $statements = [];

        $columns = [];
        $primaryKeys = [];

        foreach ($blueprint->getColumns() as $column) {
            $columns[] = $this->compileColumn($column);
            
            // Collect primary keys that aren't auto-increment (those are handled inline)
            if ($column->get('primary') && !$column->get('autoIncrement')) {
                $primaryKeys[] = $column->get('name');
            }
        }

        // Add composite primary key if needed
        if (count($primaryKeys) > 1) {
            $columns[] = 'PRIMARY KEY (' . $this->columnize($primaryKeys) . ')';
        }

        // Add foreign keys inline for SQLite
        foreach ($blueprint->getCommands() as $command) {
            if ($command['name'] === 'foreign') {
                $columns[] = $this->compileForeignInline($command);
            }
        }

        $sql = sprintf(
            '%s TABLE %s (%s)',
            $blueprint->temporary ? 'CREATE TEMPORARY' : 'CREATE',
            $this->wrapTable($blueprint->getTable()),
            implode(', ', $columns)
        );

        $statements[] = $sql;

        // Compile indexes (not foreign keys, those are inline)
        foreach ($blueprint->getCommands() as $command) {
            if (in_array($command['name'], ['unique', 'index'])) {
                $method = 'compile' . ucfirst($command['name']);
                $result = $this->$method($blueprint, $command);
                if ($result) {
                    $statements[] = $result;
                }
            }
        }

        return $statements;
    }

    /**
     * Compile a drop table command
     *
     * @param string $tableName
     * @return string
     */
    public function compileDrop(string $tableName): string
    {
        return 'DROP TABLE ' . $this->wrapTable($tableName);
    }

    /**
     * Compile a drop table if exists command
     *
     * @param string $tableName
     * @return string
     */
    public function compileDropIfExists(string $tableName): string
    {
        return 'DROP TABLE IF EXISTS ' . $this->wrapTable($tableName);
    }

    /**
     * Compile a rename table command
     *
     * @param string $from
     * @param string $to
     * @return string
     */
    public function compileRename(string $from, string $to): string
    {
        return sprintf(
            'ALTER TABLE %s RENAME TO %s',
            $this->wrapTable($from),
            $this->wrapTable($to)
        );
    }

    /**
     * Compile add column command
     *
     * @param Blueprint $blueprint
     * @return array
     */
    public function compileAdd(Blueprint $blueprint): array
    {
        $statements = [];

        foreach ($blueprint->getColumns() as $column) {
            if (!$column->get('change')) {
                $statements[] = sprintf(
                    'ALTER TABLE %s ADD COLUMN %s',
                    $this->wrapTable($blueprint->getTable()),
                    $this->compileColumn($column)
                );
            }
        }

        return $statements;
    }

    /**
     * Compile modify column command
     * Note: SQLite doesn't support MODIFY COLUMN directly, requires table recreation
     *
     * @param Blueprint $blueprint
     * @return array
     */
    public function compileModify(Blueprint $blueprint): array
    {
        // SQLite doesn't support ALTER COLUMN
        // This would require recreating the table
        return [];
    }

    /**
     * Compile drop column command
     *
     * @param string $tableName
     * @param string|array $columns
     * @return string
     */
    public function compileDropColumn(string $tableName, string|array $columns): string
    {
        $columns = is_array($columns) ? $columns : [$columns];
        $drops = array_map(fn($col) => 'DROP COLUMN ' . $this->wrap($col), $columns);

        return sprintf(
            'ALTER TABLE %s %s',
            $this->wrapTable($tableName),
            implode(', ', $drops)
        );
    }

    /**
     * Compile table exists query
     *
     * @return string
     */
    public function compileTableExists(): string
    {
        return "SELECT * FROM sqlite_master WHERE type = 'table' AND name = :table";
    }

    /**
     * Compile column listing query
     *
     * @return string
     */
    public function compileColumnListing(): string
    {
        return "PRAGMA table_info(:table)";
    }

    /**
     * Compile a foreign key inline (for CREATE TABLE)
     *
     * @param array $command
     * @return string
     */
    protected function compileForeignInline(array $command): string
    {
        $columns = is_array($command['columns']) ? $command['columns'] : [$command['columns']];

        $sql = sprintf(
            'FOREIGN KEY (%s) REFERENCES %s (%s)',
            $this->columnize($columns),
            $this->wrapTable($command['on']),
            $this->wrap($command['references'])
        );

        if (isset($command['onDelete'])) {
            $sql .= ' ON DELETE ' . $command['onDelete'];
        }

        if (isset($command['onUpdate'])) {
            $sql .= ' ON UPDATE ' . $command['onUpdate'];
        }

        return $sql;
    }

    /**
     * Compile a unique key command
     *
     * @param Blueprint $blueprint
     * @param array $command
     * @return string
     */
    protected function compileUnique(Blueprint $blueprint, array $command): string
    {
        $columns = is_array($command['columns']) ? $command['columns'] : [$command['columns']];
        $name = $command['name'] ?? $this->createIndexName('unique', $blueprint->getTable(), $columns);

        return sprintf(
            'CREATE UNIQUE INDEX %s ON %s (%s)',
            $this->wrap($name),
            $this->wrapTable($blueprint->getTable()),
            $this->columnize($columns)
        );
    }

    /**
     * Compile an index command
     *
     * @param Blueprint $blueprint
     * @param array $command
     * @return string
     */
    protected function compileIndex(Blueprint $blueprint, array $command): string
    {
        $columns = is_array($command['columns']) ? $command['columns'] : [$command['columns']];
        $name = $command['name'] ?? $this->createIndexName('index', $blueprint->getTable(), $columns);

        return sprintf(
            'CREATE INDEX %s ON %s (%s)',
            $this->wrap($name),
            $this->wrapTable($blueprint->getTable()),
            $this->columnize($columns)
        );
    }

    // ==================== SQLite-specific type overrides ====================

    /**
     * Get the SQL for an integer type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeInteger(ColumnDefinition $column): string
    {
        return 'INTEGER';
    }

    /**
     * Get the SQL for a big integer type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeBigInteger(ColumnDefinition $column): string
    {
        return 'INTEGER';
    }

    /**
     * Get the SQL for a small integer type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeSmallInteger(ColumnDefinition $column): string
    {
        return 'INTEGER';
    }

    /**
     * Get the SQL for a tiny integer type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeTinyInteger(ColumnDefinition $column): string
    {
        return 'INTEGER';
    }

    /**
     * Get the SQL for a medium integer type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeMediumInteger(ColumnDefinition $column): string
    {
        return 'INTEGER';
    }

    /**
     * Get the SQL for a float type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeFloat(ColumnDefinition $column): string
    {
        return 'REAL';
    }

    /**
     * Get the SQL for a double type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeDouble(ColumnDefinition $column): string
    {
        return 'REAL';
    }

    /**
     * Get the SQL for a decimal type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeDecimal(ColumnDefinition $column): string
    {
        return 'NUMERIC';
    }

    /**
     * Get the SQL for a boolean type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeBoolean(ColumnDefinition $column): string
    {
        return 'INTEGER';
    }

    /**
     * Get the SQL for a datetime type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeDateTime(ColumnDefinition $column): string
    {
        return 'DATETIME';
    }

    /**
     * Get the SQL for a timestamp type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeTimestamp(ColumnDefinition $column): string
    {
        return 'DATETIME';
    }

    /**
     * Get the SQL for a binary type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeBinary(ColumnDefinition $column): string
    {
        return 'BLOB';
    }

    /**
     * Get the SQL for a JSON type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeJson(ColumnDefinition $column): string
    {
        return 'TEXT';
    }

    /**
     * Get the SQL for an enum type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeEnum(ColumnDefinition $column): string
    {
        return 'TEXT';
    }

    // ==================== Column Modifiers ====================

    /**
     * Get the SQL for a nullable column modifier
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function modifyNullable(ColumnDefinition $column): string
    {
        if ($column->get('nullable') === false) {
            return ' NOT NULL';
        }
        return '';
    }

    /**
     * Get the SQL for a default column modifier
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function modifyDefault(ColumnDefinition $column): string
    {
        if ($column->get('useCurrent')) {
            return ' DEFAULT CURRENT_TIMESTAMP';
        }

        $default = $column->get('default');
        if ($default !== null) {
            return ' DEFAULT ' . $this->getDefaultValue($default);
        }

        return '';
    }

    /**
     * Get the SQL for an increment column modifier
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function modifyIncrement(ColumnDefinition $column): string
    {
        if ($column->get('autoIncrement')) {
            return ' PRIMARY KEY AUTOINCREMENT';
        }
        
        // Handle non-autoincrement primary keys
        if ($column->get('primary') && !$column->get('autoIncrement')) {
            return ' PRIMARY KEY';
        }
        
        return '';
    }
}
