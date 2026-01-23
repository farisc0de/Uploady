<?php

namespace Farisc0de\PhpMigration\Schema\Grammars;

use Farisc0de\PhpMigration\Schema\Blueprint;
use Farisc0de\PhpMigration\Schema\ColumnDefinition;

/**
 * Class PostgresGrammar
 * 
 * PostgreSQL-specific SQL grammar
 */
class PostgresGrammar extends Grammar
{
    /**
     * The column modifiers
     *
     * @var array
     */
    protected array $modifiers = [
        'Collate', 'Nullable', 'Default', 'VirtualAs', 'StoredAs', 'Increment'
    ];

    /**
     * Get the driver name
     *
     * @return string
     */
    public function getDriverName(): string
    {
        return 'pgsql';
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
        foreach ($blueprint->getColumns() as $column) {
            $columns[] = $this->compileColumn($column);
        }

        $sql = sprintf(
            '%s TABLE %s (%s)',
            $blueprint->temporary ? 'CREATE TEMPORARY' : 'CREATE',
            $this->wrapTable($blueprint->getTable()),
            implode(', ', $columns)
        );

        $statements[] = $sql;

        // Add comment if specified
        if ($blueprint->comment) {
            $statements[] = sprintf(
                "COMMENT ON TABLE %s IS '%s'",
                $this->wrapTable($blueprint->getTable()),
                addslashes($blueprint->comment)
            );
        }

        // Compile indexes and constraints
        foreach ($blueprint->getCommands() as $command) {
            $method = 'compile' . ucfirst($command['name']);
            if (method_exists($this, $method)) {
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
     *
     * @param Blueprint $blueprint
     * @return array
     */
    public function compileModify(Blueprint $blueprint): array
    {
        $statements = [];

        foreach ($blueprint->getColumns() as $column) {
            if ($column->get('change')) {
                $columnName = $this->wrap($column->get('name'));
                $tableName = $this->wrapTable($blueprint->getTable());

                // Change type
                $statements[] = sprintf(
                    'ALTER TABLE %s ALTER COLUMN %s TYPE %s',
                    $tableName,
                    $columnName,
                    $this->getType($column)
                );

                // Change nullable
                if ($column->get('nullable') === true) {
                    $statements[] = sprintf(
                        'ALTER TABLE %s ALTER COLUMN %s DROP NOT NULL',
                        $tableName,
                        $columnName
                    );
                } elseif ($column->get('nullable') === false) {
                    $statements[] = sprintf(
                        'ALTER TABLE %s ALTER COLUMN %s SET NOT NULL',
                        $tableName,
                        $columnName
                    );
                }

                // Change default
                $default = $column->get('default');
                if ($default !== null) {
                    $statements[] = sprintf(
                        'ALTER TABLE %s ALTER COLUMN %s SET DEFAULT %s',
                        $tableName,
                        $columnName,
                        $this->getDefaultValue($default)
                    );
                }
            }
        }

        return $statements;
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
        return "SELECT * FROM information_schema.tables WHERE table_catalog = :database AND table_schema = 'public' AND table_name = :table AND table_type = 'BASE TABLE'";
    }

    /**
     * Compile column listing query
     *
     * @return string
     */
    public function compileColumnListing(): string
    {
        return "SELECT column_name FROM information_schema.columns WHERE table_catalog = :database AND table_schema = 'public' AND table_name = :table";
    }

    // ==================== PostgreSQL-specific type overrides ====================

    /**
     * Get the SQL for an integer type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeInteger(ColumnDefinition $column): string
    {
        return $column->get('autoIncrement') ? 'SERIAL' : 'INTEGER';
    }

    /**
     * Get the SQL for a big integer type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeBigInteger(ColumnDefinition $column): string
    {
        return $column->get('autoIncrement') ? 'BIGSERIAL' : 'BIGINT';
    }

    /**
     * Get the SQL for a small integer type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeSmallInteger(ColumnDefinition $column): string
    {
        return $column->get('autoIncrement') ? 'SMALLSERIAL' : 'SMALLINT';
    }

    /**
     * Get the SQL for a tiny integer type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeTinyInteger(ColumnDefinition $column): string
    {
        return 'SMALLINT';
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
     * Get the SQL for a boolean type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeBoolean(ColumnDefinition $column): string
    {
        return 'BOOLEAN';
    }

    /**
     * Get the SQL for a text type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeText(ColumnDefinition $column): string
    {
        return 'TEXT';
    }

    /**
     * Get the SQL for a tiny text type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeTinyText(ColumnDefinition $column): string
    {
        return 'TEXT';
    }

    /**
     * Get the SQL for a medium text type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeMediumText(ColumnDefinition $column): string
    {
        return 'TEXT';
    }

    /**
     * Get the SQL for a long text type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeLongText(ColumnDefinition $column): string
    {
        return 'TEXT';
    }

    /**
     * Get the SQL for a binary type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeBinary(ColumnDefinition $column): string
    {
        return 'BYTEA';
    }

    /**
     * Get the SQL for a blob type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeBlob(ColumnDefinition $column): string
    {
        return 'BYTEA';
    }

    /**
     * Get the SQL for a UUID type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeUuid(ColumnDefinition $column): string
    {
        return 'UUID';
    }

    /**
     * Get the SQL for a JSON type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeJson(ColumnDefinition $column): string
    {
        return 'JSON';
    }

    /**
     * Get the SQL for a JSONB type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeJsonb(ColumnDefinition $column): string
    {
        return 'JSONB';
    }

    /**
     * Get the SQL for a datetime type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeDateTime(ColumnDefinition $column): string
    {
        $precision = $column->get('precision', 0);
        return $precision > 0 ? "TIMESTAMP({$precision}) WITHOUT TIME ZONE" : 'TIMESTAMP WITHOUT TIME ZONE';
    }

    /**
     * Get the SQL for a timestamp type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeTimestamp(ColumnDefinition $column): string
    {
        $precision = $column->get('precision', 0);
        return $precision > 0 ? "TIMESTAMP({$precision}) WITHOUT TIME ZONE" : 'TIMESTAMP WITHOUT TIME ZONE';
    }

    /**
     * Get the SQL for an enum type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeEnum(ColumnDefinition $column): string
    {
        $allowed = $column->get('allowed', []);
        $values = array_map(fn($v) => "'{$v}'", $allowed);
        return 'VARCHAR(255) CHECK (' . $this->wrap($column->get('name')) . ' IN (' . implode(', ', $values) . '))';
    }

    // ==================== Index Commands ====================

    /**
     * Compile a primary key command
     *
     * @param Blueprint $blueprint
     * @param array $command
     * @return string
     */
    protected function compilePrimary(Blueprint $blueprint, array $command): string
    {
        $columns = is_array($command['columns']) ? $command['columns'] : [$command['columns']];

        return sprintf(
            'ALTER TABLE %s ADD PRIMARY KEY (%s)',
            $this->wrapTable($blueprint->getTable()),
            $this->columnize($columns)
        );
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
            'ALTER TABLE %s ADD CONSTRAINT %s UNIQUE (%s)',
            $this->wrapTable($blueprint->getTable()),
            $this->wrap($name),
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

    /**
     * Compile a foreign key command
     *
     * @param Blueprint $blueprint
     * @param array $command
     * @return string
     */
    protected function compileForeign(Blueprint $blueprint, array $command): string
    {
        $columns = is_array($command['columns']) ? $command['columns'] : [$command['columns']];
        $name = $command['name'] ?? $this->createIndexName('foreign', $blueprint->getTable(), $columns);

        $sql = sprintf(
            'ALTER TABLE %s ADD CONSTRAINT %s FOREIGN KEY (%s) REFERENCES %s (%s)',
            $this->wrapTable($blueprint->getTable()),
            $this->wrap($name),
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

        if (isset($command['deferrable']) && $command['deferrable']) {
            $sql .= ' DEFERRABLE';
            if (isset($command['initiallyDeferred']) && $command['initiallyDeferred']) {
                $sql .= ' INITIALLY DEFERRED';
            }
        }

        return $sql;
    }

    // ==================== Column Modifiers ====================

    /**
     * Get the SQL for a collate column modifier
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function modifyCollate(ColumnDefinition $column): string
    {
        if ($collation = $column->get('collation')) {
            return ' COLLATE "' . $collation . '"';
        }
        return '';
    }

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
     * Get the SQL for a virtual as column modifier
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function modifyVirtualAs(ColumnDefinition $column): string
    {
        if ($expression = $column->get('virtualAs')) {
            return " GENERATED ALWAYS AS ({$expression})";
        }
        return '';
    }

    /**
     * Get the SQL for a stored as column modifier
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function modifyStoredAs(ColumnDefinition $column): string
    {
        if ($expression = $column->get('storedAs')) {
            return " GENERATED ALWAYS AS ({$expression}) STORED";
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
        // PostgreSQL uses SERIAL types for auto-increment, handled in type methods
        return '';
    }
}
