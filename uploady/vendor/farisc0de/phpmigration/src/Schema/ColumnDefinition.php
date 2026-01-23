<?php

namespace Farisc0de\PhpMigration\Schema;

/**
 * Class ColumnDefinition
 * 
 * Represents a column definition with fluent modifiers
 */
class ColumnDefinition
{
    /**
     * The column attributes
     *
     * @var array
     */
    protected array $attributes = [];

    /**
     * Create a new column definition
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * Get an attribute value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    /**
     * Set an attribute value
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set(string $key, mixed $value): self
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    /**
     * Get all attributes
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Place the column "after" another column (MySQL)
     *
     * @param string $column
     * @return $this
     */
    public function after(string $column): self
    {
        return $this->set('after', $column);
    }

    /**
     * Set INTEGER column as auto-increment (primary key)
     *
     * @return $this
     */
    public function autoIncrement(): self
    {
        return $this->set('autoIncrement', true);
    }

    /**
     * Set the character set for the column
     *
     * @param string $charset
     * @return $this
     */
    public function charset(string $charset): self
    {
        return $this->set('charset', $charset);
    }

    /**
     * Set the collation for the column
     *
     * @param string $collation
     * @return $this
     */
    public function collation(string $collation): self
    {
        return $this->set('collation', $collation);
    }

    /**
     * Add a comment to the column
     *
     * @param string $comment
     * @return $this
     */
    public function comment(string $comment): self
    {
        return $this->set('comment', $comment);
    }

    /**
     * Specify a "default" value for the column
     *
     * @param mixed $value
     * @return $this
     */
    public function default(mixed $value): self
    {
        return $this->set('default', $value);
    }

    /**
     * Place the column "first" in the table (MySQL)
     *
     * @return $this
     */
    public function first(): self
    {
        return $this->set('first', true);
    }

    /**
     * Set the starting value of an auto-incrementing field (MySQL/PostgreSQL)
     *
     * @param int $startingValue
     * @return $this
     */
    public function from(int $startingValue): self
    {
        return $this->set('from', $startingValue);
    }

    /**
     * Set the column as invisible (MySQL)
     *
     * @return $this
     */
    public function invisible(): self
    {
        return $this->set('invisible', true);
    }

    /**
     * Allow NULL values to be inserted into the column
     *
     * @param bool $value
     * @return $this
     */
    public function nullable(bool $value = true): self
    {
        return $this->set('nullable', $value);
    }

    /**
     * Mark the column as a primary key
     *
     * @return $this
     */
    public function primary(): self
    {
        return $this->set('primary', true);
    }

    /**
     * Rename the column (used during modification)
     *
     * @param string $to
     * @return $this
     */
    public function renameTo(string $to): self
    {
        return $this->set('renameTo', $to);
    }

    /**
     * Add a stored generated column (MySQL/PostgreSQL/SQLite)
     *
     * @param string $expression
     * @return $this
     */
    public function storedAs(string $expression): self
    {
        return $this->set('storedAs', $expression);
    }

    /**
     * Mark the column as unique
     *
     * @return $this
     */
    public function unique(): self
    {
        return $this->set('unique', true);
    }

    /**
     * Set the INTEGER column as UNSIGNED (MySQL)
     *
     * @return $this
     */
    public function unsigned(): self
    {
        return $this->set('unsigned', true);
    }

    /**
     * Set the TIMESTAMP column to use CURRENT_TIMESTAMP as default value
     *
     * @return $this
     */
    public function useCurrent(): self
    {
        return $this->set('useCurrent', true);
    }

    /**
     * Set the TIMESTAMP column to use CURRENT_TIMESTAMP when updating (MySQL)
     *
     * @return $this
     */
    public function useCurrentOnUpdate(): self
    {
        return $this->set('useCurrentOnUpdate', true);
    }

    /**
     * Add a virtual generated column (MySQL/PostgreSQL/SQLite)
     *
     * @param string $expression
     * @return $this
     */
    public function virtualAs(string $expression): self
    {
        return $this->set('virtualAs', $expression);
    }

    /**
     * Create an index on the column
     *
     * @param string|null $name
     * @return $this
     */
    public function index(?string $name = null): self
    {
        return $this->set('index', $name ?? true);
    }

    /**
     * Create a fulltext index on the column
     *
     * @param string|null $name
     * @return $this
     */
    public function fulltext(?string $name = null): self
    {
        return $this->set('fulltext', $name ?? true);
    }

    /**
     * Create a spatial index on the column
     *
     * @param string|null $name
     * @return $this
     */
    public function spatialIndex(?string $name = null): self
    {
        return $this->set('spatialIndex', $name ?? true);
    }

    /**
     * Set the column as NOT NULL
     *
     * @return $this
     */
    public function notNull(): self
    {
        return $this->nullable(false);
    }

    /**
     * Mark the column for modification (used in table alterations)
     *
     * @return $this
     */
    public function change(): self
    {
        return $this->set('change', true);
    }

    /**
     * Set a check constraint on the column
     *
     * @param string $expression
     * @return $this
     */
    public function check(string $expression): self
    {
        return $this->set('check', $expression);
    }

    /**
     * Set the column as ZEROFILL (MySQL)
     *
     * @return $this
     */
    public function zerofill(): self
    {
        return $this->set('zerofill', true);
    }

    /**
     * Set the column as BINARY
     *
     * @return $this
     */
    public function binary(): self
    {
        return $this->set('binary', true);
    }

    /**
     * Allow dynamic property access
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        return $this->get($name);
    }

    /**
     * Allow dynamic property setting
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set(string $name, mixed $value): void
    {
        $this->set($name, $value);
    }

    /**
     * Check if an attribute exists
     *
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }
}
