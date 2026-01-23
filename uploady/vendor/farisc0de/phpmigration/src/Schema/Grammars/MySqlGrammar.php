<?php

namespace Farisc0de\PhpMigration\Schema\Grammars;

use Farisc0de\PhpMigration\Schema\Blueprint;
use Farisc0de\PhpMigration\Schema\ColumnDefinition;

/**
 * Class MySqlGrammar
 * 
 * MySQL-specific SQL grammar
 */
class MySqlGrammar extends Grammar
{
    /**
     * The column modifiers
     *
     * @var array
     */
    protected array $modifiers = [
        'Unsigned', 'Charset', 'Collate', 'Nullable', 'Default',
        'OnUpdate', 'Increment', 'Comment', 'After', 'First',
        'Invisible', 'VirtualAs', 'StoredAs'
    ];

    /**
     * Get the driver name
     *
     * @return string
     */
    public function getDriverName(): string
    {
        return 'mysql';
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

        return '`' . str_replace('`', '``', $value) . '`';
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

        // Add table options
        if ($blueprint->engine) {
            $sql .= ' ENGINE = ' . $blueprint->engine;
        }

        if ($blueprint->charset) {
            $sql .= ' DEFAULT CHARACTER SET ' . $blueprint->charset;
        }

        if ($blueprint->collation) {
            $sql .= ' COLLATE ' . $blueprint->collation;
        }

        if ($blueprint->comment) {
            $sql .= " COMMENT = '" . addslashes($blueprint->comment) . "'";
        }

        $statements[] = $sql;

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
            'RENAME TABLE %s TO %s',
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
                    'ALTER TABLE %s ADD %s',
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
                $statements[] = sprintf(
                    'ALTER TABLE %s MODIFY %s',
                    $this->wrapTable($blueprint->getTable()),
                    $this->compileColumn($column)
                );
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
        return "SELECT * FROM information_schema.tables WHERE table_schema = :database AND table_name = :table AND table_type = 'BASE TABLE'";
    }

    /**
     * Compile column listing query
     *
     * @return string
     */
    public function compileColumnListing(): string
    {
        return 'SELECT column_name FROM information_schema.columns WHERE table_schema = :database AND table_name = :table';
    }

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
            'ALTER TABLE %s ADD UNIQUE %s (%s)',
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
            'ALTER TABLE %s ADD INDEX %s (%s)',
            $this->wrapTable($blueprint->getTable()),
            $this->wrap($name),
            $this->columnize($columns)
        );
    }

    /**
     * Compile a fulltext index command
     *
     * @param Blueprint $blueprint
     * @param array $command
     * @return string
     */
    protected function compileFullText(Blueprint $blueprint, array $command): string
    {
        $columns = is_array($command['columns']) ? $command['columns'] : [$command['columns']];
        $name = $command['name'] ?? $this->createIndexName('fulltext', $blueprint->getTable(), $columns);

        return sprintf(
            'ALTER TABLE %s ADD FULLTEXT %s (%s)',
            $this->wrapTable($blueprint->getTable()),
            $this->wrap($name),
            $this->columnize($columns)
        );
    }

    /**
     * Compile a spatial index command
     *
     * @param Blueprint $blueprint
     * @param array $command
     * @return string
     */
    protected function compileSpatialIndex(Blueprint $blueprint, array $command): string
    {
        $columns = is_array($command['columns']) ? $command['columns'] : [$command['columns']];
        $name = $command['name'] ?? $this->createIndexName('spatial', $blueprint->getTable(), $columns);

        return sprintf(
            'ALTER TABLE %s ADD SPATIAL INDEX %s (%s)',
            $this->wrapTable($blueprint->getTable()),
            $this->wrap($name),
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

        return $sql;
    }

    /**
     * Compile a drop primary key command
     *
     * @param Blueprint $blueprint
     * @param array $command
     * @return string
     */
    protected function compileDropPrimary(Blueprint $blueprint, array $command): string
    {
        return sprintf(
            'ALTER TABLE %s DROP PRIMARY KEY',
            $this->wrapTable($blueprint->getTable())
        );
    }

    /**
     * Compile a drop unique key command
     *
     * @param Blueprint $blueprint
     * @param array $command
     * @return string
     */
    protected function compileDropUnique(Blueprint $blueprint, array $command): string
    {
        $columns = is_array($command['columns']) ? $command['columns'] : [$command['columns']];
        $name = is_string($command['columns']) && !str_contains($command['columns'], '_')
            ? $command['columns']
            : $this->createIndexName('unique', $blueprint->getTable(), $columns);

        return sprintf(
            'ALTER TABLE %s DROP INDEX %s',
            $this->wrapTable($blueprint->getTable()),
            $this->wrap($name)
        );
    }

    /**
     * Compile a drop index command
     *
     * @param Blueprint $blueprint
     * @param array $command
     * @return string
     */
    protected function compileDropIndex(Blueprint $blueprint, array $command): string
    {
        $columns = is_array($command['columns']) ? $command['columns'] : [$command['columns']];
        $name = is_string($command['columns']) && !str_contains($command['columns'], '_')
            ? $command['columns']
            : $this->createIndexName('index', $blueprint->getTable(), $columns);

        return sprintf(
            'ALTER TABLE %s DROP INDEX %s',
            $this->wrapTable($blueprint->getTable()),
            $this->wrap($name)
        );
    }

    /**
     * Compile a drop foreign key command
     *
     * @param Blueprint $blueprint
     * @param array $command
     * @return string
     */
    protected function compileDropForeign(Blueprint $blueprint, array $command): string
    {
        $columns = is_array($command['columns']) ? $command['columns'] : [$command['columns']];
        $name = is_string($command['columns']) && !str_contains($command['columns'], '_')
            ? $command['columns']
            : $this->createIndexName('foreign', $blueprint->getTable(), $columns);

        return sprintf(
            'ALTER TABLE %s DROP FOREIGN KEY %s',
            $this->wrapTable($blueprint->getTable()),
            $this->wrap($name)
        );
    }

    /**
     * Compile a rename column command
     *
     * @param Blueprint $blueprint
     * @param array $command
     * @return string
     */
    protected function compileRenameColumn(Blueprint $blueprint, array $command): string
    {
        return sprintf(
            'ALTER TABLE %s RENAME COLUMN %s TO %s',
            $this->wrapTable($blueprint->getTable()),
            $this->wrap($command['from']),
            $this->wrap($command['to'])
        );
    }

    /**
     * Compile a drop column command
     *
     * @param Blueprint $blueprint
     * @param array $command
     * @return string
     */
    protected function compileDropColumnCommand(Blueprint $blueprint, array $command): string
    {
        $columns = is_array($command['columns']) ? $command['columns'] : [$command['columns']];
        return $this->compileDropColumn($blueprint->getTable(), $columns);
    }

    // ==================== Column Modifiers ====================

    /**
     * Get the SQL for an unsigned column modifier
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function modifyUnsigned(ColumnDefinition $column): string
    {
        if ($column->get('unsigned')) {
            return ' UNSIGNED';
        }
        return '';
    }

    /**
     * Get the SQL for a charset column modifier
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function modifyCharset(ColumnDefinition $column): string
    {
        if ($charset = $column->get('charset')) {
            return ' CHARACTER SET ' . $charset;
        }
        return '';
    }

    /**
     * Get the SQL for a collate column modifier
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function modifyCollate(ColumnDefinition $column): string
    {
        if ($collation = $column->get('collation')) {
            return ' COLLATE ' . $collation;
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

        if ($column->get('nullable') === true) {
            return ' NULL';
        }

        // Default to NOT NULL for primary keys and auto-increment
        if ($column->get('primary') || $column->get('autoIncrement')) {
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
     * Get the SQL for an on update column modifier
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function modifyOnUpdate(ColumnDefinition $column): string
    {
        if ($column->get('useCurrentOnUpdate')) {
            return ' ON UPDATE CURRENT_TIMESTAMP';
        }
        return '';
    }

    /**
     * Get the SQL for an auto-increment column modifier
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function modifyIncrement(ColumnDefinition $column): string
    {
        if ($column->get('autoIncrement')) {
            $sql = ' AUTO_INCREMENT';
            if ($column->get('primary')) {
                $sql .= ' PRIMARY KEY';
            }
            return $sql;
        }
        return '';
    }

    /**
     * Get the SQL for a comment column modifier
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function modifyComment(ColumnDefinition $column): string
    {
        if ($comment = $column->get('comment')) {
            return " COMMENT '" . addslashes($comment) . "'";
        }
        return '';
    }

    /**
     * Get the SQL for an after column modifier
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function modifyAfter(ColumnDefinition $column): string
    {
        if ($after = $column->get('after')) {
            return ' AFTER ' . $this->wrap($after);
        }
        return '';
    }

    /**
     * Get the SQL for a first column modifier
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function modifyFirst(ColumnDefinition $column): string
    {
        if ($column->get('first')) {
            return ' FIRST';
        }
        return '';
    }

    /**
     * Get the SQL for an invisible column modifier
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function modifyInvisible(ColumnDefinition $column): string
    {
        if ($column->get('invisible')) {
            return ' INVISIBLE';
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
            return " AS ({$expression}) VIRTUAL";
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
            return " AS ({$expression}) STORED";
        }
        return '';
    }
}
