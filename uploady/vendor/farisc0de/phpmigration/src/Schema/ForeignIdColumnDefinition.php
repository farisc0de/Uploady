<?php

namespace Farisc0de\PhpMigration\Schema;

/**
 * Class ForeignIdColumnDefinition
 * 
 * Provides a fluent interface for defining foreign ID columns with constraints
 */
class ForeignIdColumnDefinition
{
    /**
     * The blueprint instance
     *
     * @var Blueprint
     */
    protected Blueprint $blueprint;

    /**
     * The underlying column definition
     *
     * @var ColumnDefinition
     */
    protected ColumnDefinition $column;

    /**
     * Create a new foreign ID column definition
     *
     * @param Blueprint $blueprint
     * @param ColumnDefinition $column
     */
    public function __construct(Blueprint $blueprint, ColumnDefinition $column)
    {
        $this->blueprint = $blueprint;
        $this->column = $column;
    }

    /**
     * Create a foreign key constraint on this column referencing the "id" column
     * of the conventionally related table
     *
     * @param string|null $table
     * @param string $column
     * @return ForeignKeyDefinition
     */
    public function constrained(?string $table = null, string $column = 'id'): ForeignKeyDefinition
    {
        $columnName = $this->column->get('name');
        
        if ($table === null) {
            $table = $this->guessTableName($columnName);
        }

        return $this->blueprint
            ->foreign($columnName)
            ->references($column)
            ->on($table);
    }

    /**
     * Guess the table name from the column name
     *
     * @param string $columnName
     * @return string
     */
    protected function guessTableName(string $columnName): string
    {
        if (str_ends_with($columnName, '_id')) {
            $table = substr($columnName, 0, -3);
            return $this->pluralize($table);
        }

        return $columnName;
    }

    /**
     * Simple pluralization (basic implementation)
     *
     * @param string $word
     * @return string
     */
    protected function pluralize(string $word): string
    {
        $irregulars = [
            'person' => 'people',
            'child' => 'children',
            'man' => 'men',
            'woman' => 'women',
            'tooth' => 'teeth',
            'foot' => 'feet',
            'mouse' => 'mice',
            'goose' => 'geese',
        ];

        if (isset($irregulars[$word])) {
            return $irregulars[$word];
        }

        if (str_ends_with($word, 'y') && !in_array($word[-2] ?? '', ['a', 'e', 'i', 'o', 'u'])) {
            return substr($word, 0, -1) . 'ies';
        }

        if (str_ends_with($word, 's') || str_ends_with($word, 'x') || 
            str_ends_with($word, 'z') || str_ends_with($word, 'ch') || 
            str_ends_with($word, 'sh')) {
            return $word . 'es';
        }

        return $word . 's';
    }

    /**
     * Allow NULL values
     *
     * @return $this
     */
    public function nullable(): self
    {
        $this->column->nullable();
        return $this;
    }

    /**
     * Set a default value
     *
     * @param mixed $value
     * @return $this
     */
    public function default(mixed $value): self
    {
        $this->column->default($value);
        return $this;
    }

    /**
     * Add a comment
     *
     * @param string $comment
     * @return $this
     */
    public function comment(string $comment): self
    {
        $this->column->comment($comment);
        return $this;
    }

    /**
     * Place the column after another column
     *
     * @param string $column
     * @return $this
     */
    public function after(string $column): self
    {
        $this->column->after($column);
        return $this;
    }

    /**
     * Place the column first
     *
     * @return $this
     */
    public function first(): self
    {
        $this->column->first();
        return $this;
    }

    /**
     * Create an index on the column
     *
     * @param string|null $name
     * @return $this
     */
    public function index(?string $name = null): self
    {
        $this->column->index($name);
        return $this;
    }

    /**
     * Get the underlying column definition
     *
     * @return ColumnDefinition
     */
    public function getColumn(): ColumnDefinition
    {
        return $this->column;
    }
}
