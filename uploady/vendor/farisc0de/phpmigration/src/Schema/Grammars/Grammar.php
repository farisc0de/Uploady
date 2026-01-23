<?php

namespace Farisc0de\PhpMigration\Schema\Grammars;

use Farisc0de\PhpMigration\Contracts\SchemaGrammarInterface;
use Farisc0de\PhpMigration\Schema\Blueprint;
use Farisc0de\PhpMigration\Schema\ColumnDefinition;

/**
 * Class Grammar
 * 
 * Base grammar class for SQL generation
 */
abstract class Grammar implements SchemaGrammarInterface
{
    /**
     * The grammar table prefix
     *
     * @var string
     */
    protected string $tablePrefix = '';

    /**
     * The column modifiers
     *
     * @var array
     */
    protected array $modifiers = [];

    /**
     * The column type mappings
     *
     * @var array
     */
    protected array $typeMap = [];

    /**
     * Set the table prefix
     *
     * @param string $prefix
     * @return void
     */
    public function setTablePrefix(string $prefix): void
    {
        $this->tablePrefix = $prefix;
    }

    /**
     * Get the table prefix
     *
     * @return string
     */
    public function getTablePrefix(): string
    {
        return $this->tablePrefix;
    }

    /**
     * Wrap a table in keyword identifiers
     *
     * @param string $table
     * @return string
     */
    public function wrapTable(string $table): string
    {
        return $this->wrap($this->tablePrefix . $table);
    }

    /**
     * Wrap a value in keyword identifiers
     *
     * @param string $value
     * @return string
     */
    abstract public function wrap(string $value): string;

    /**
     * Get the column type SQL
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function getType(ColumnDefinition $column): string
    {
        $type = $column->get('type');
        $method = 'type' . ucfirst($type);

        if (method_exists($this, $method)) {
            return $this->$method($column);
        }

        return $this->typeMap[$type] ?? $type;
    }

    /**
     * Add column modifiers to the definition
     *
     * @param string $sql
     * @param ColumnDefinition $column
     * @return string
     */
    protected function addModifiers(string $sql, ColumnDefinition $column): string
    {
        foreach ($this->modifiers as $modifier) {
            $method = "modify{$modifier}";
            if (method_exists($this, $method)) {
                $sql .= $this->$method($column);
            }
        }

        return $sql;
    }

    /**
     * Compile a column definition
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function compileColumn(ColumnDefinition $column): string
    {
        $sql = $this->wrap($column->get('name')) . ' ' . $this->getType($column);
        return $this->addModifiers($sql, $column);
    }

    /**
     * Get the SQL for the column data type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeInteger(ColumnDefinition $column): string
    {
        return 'INT';
    }

    /**
     * Get the SQL for a tiny integer type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeTinyInteger(ColumnDefinition $column): string
    {
        return 'TINYINT';
    }

    /**
     * Get the SQL for a small integer type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeSmallInteger(ColumnDefinition $column): string
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
        return 'MEDIUMINT';
    }

    /**
     * Get the SQL for a big integer type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeBigInteger(ColumnDefinition $column): string
    {
        return 'BIGINT';
    }

    /**
     * Get the SQL for a string type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeString(ColumnDefinition $column): string
    {
        $length = $column->get('length', 255);
        return "VARCHAR({$length})";
    }

    /**
     * Get the SQL for a char type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeChar(ColumnDefinition $column): string
    {
        $length = $column->get('length', 255);
        return "CHAR({$length})";
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
        return 'TINYTEXT';
    }

    /**
     * Get the SQL for a medium text type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeMediumText(ColumnDefinition $column): string
    {
        return 'MEDIUMTEXT';
    }

    /**
     * Get the SQL for a long text type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeLongText(ColumnDefinition $column): string
    {
        return 'LONGTEXT';
    }

    /**
     * Get the SQL for a float type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeFloat(ColumnDefinition $column): string
    {
        $precision = $column->get('precision');
        $scale = $column->get('scale');

        if ($precision && $scale) {
            return "FLOAT({$precision}, {$scale})";
        }

        return 'FLOAT';
    }

    /**
     * Get the SQL for a double type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeDouble(ColumnDefinition $column): string
    {
        $precision = $column->get('precision');
        $scale = $column->get('scale');

        if ($precision && $scale) {
            return "DOUBLE({$precision}, {$scale})";
        }

        return 'DOUBLE';
    }

    /**
     * Get the SQL for a decimal type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeDecimal(ColumnDefinition $column): string
    {
        $precision = $column->get('precision', 8);
        $scale = $column->get('scale', 2);
        return "DECIMAL({$precision}, {$scale})";
    }

    /**
     * Get the SQL for a boolean type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeBoolean(ColumnDefinition $column): string
    {
        return 'TINYINT(1)';
    }

    /**
     * Get the SQL for a date type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeDate(ColumnDefinition $column): string
    {
        return 'DATE';
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
        return $precision > 0 ? "DATETIME({$precision})" : 'DATETIME';
    }

    /**
     * Get the SQL for a time type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeTime(ColumnDefinition $column): string
    {
        $precision = $column->get('precision', 0);
        return $precision > 0 ? "TIME({$precision})" : 'TIME';
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
        return $precision > 0 ? "TIMESTAMP({$precision})" : 'TIMESTAMP';
    }

    /**
     * Get the SQL for a year type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeYear(ColumnDefinition $column): string
    {
        return 'YEAR';
    }

    /**
     * Get the SQL for a binary type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeBinary(ColumnDefinition $column): string
    {
        $length = $column->get('length');
        return $length ? "BINARY({$length})" : 'BLOB';
    }

    /**
     * Get the SQL for a blob type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeBlob(ColumnDefinition $column): string
    {
        return 'BLOB';
    }

    /**
     * Get the SQL for a tiny blob type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeTinyBlob(ColumnDefinition $column): string
    {
        return 'TINYBLOB';
    }

    /**
     * Get the SQL for a medium blob type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeMediumBlob(ColumnDefinition $column): string
    {
        return 'MEDIUMBLOB';
    }

    /**
     * Get the SQL for a long blob type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeLongBlob(ColumnDefinition $column): string
    {
        return 'LONGBLOB';
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
        return 'ENUM(' . implode(', ', $values) . ')';
    }

    /**
     * Get the SQL for a set type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeSet(ColumnDefinition $column): string
    {
        $allowed = $column->get('allowed', []);
        $values = array_map(fn($v) => "'{$v}'", $allowed);
        return 'SET(' . implode(', ', $values) . ')';
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
     * Get the SQL for a UUID type
     *
     * @param ColumnDefinition $column
     * @return string
     */
    protected function typeUuid(ColumnDefinition $column): string
    {
        return 'CHAR(36)';
    }

    /**
     * Columnize an array of column names
     *
     * @param array $columns
     * @return string
     */
    protected function columnize(array $columns): string
    {
        return implode(', ', array_map([$this, 'wrap'], $columns));
    }

    /**
     * Get the value of a default expression
     *
     * @param mixed $value
     * @return string
     */
    protected function getDefaultValue(mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return "'{$value}'";
    }

    /**
     * Create an index name for the given type
     *
     * @param string $type
     * @param string $table
     * @param array $columns
     * @return string
     */
    protected function createIndexName(string $type, string $table, array $columns): string
    {
        $index = strtolower($table . '_' . implode('_', $columns) . '_' . $type);
        return str_replace(['-', '.'], '_', $index);
    }
}
