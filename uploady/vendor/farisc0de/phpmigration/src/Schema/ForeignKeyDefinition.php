<?php

namespace Farisc0de\PhpMigration\Schema;

/**
 * Class ForeignKeyDefinition
 * 
 * Provides a fluent interface for defining foreign key constraints
 */
class ForeignKeyDefinition
{
    /**
     * The blueprint instance
     *
     * @var Blueprint
     */
    protected Blueprint $blueprint;

    /**
     * The command array reference
     *
     * @var array
     */
    protected array $command;

    /**
     * Create a new foreign key definition
     *
     * @param Blueprint $blueprint
     * @param array $command
     */
    public function __construct(Blueprint $blueprint, array &$command)
    {
        $this->blueprint = $blueprint;
        $this->command = &$command;
    }

    /**
     * Specify the referenced table and column
     *
     * @param string $column
     * @return $this
     */
    public function references(string $column): self
    {
        $this->command['references'] = $column;
        return $this;
    }

    /**
     * Specify the referenced table
     *
     * @param string $table
     * @return $this
     */
    public function on(string $table): self
    {
        $this->command['on'] = $table;
        return $this;
    }

    /**
     * Set the ON DELETE action
     *
     * @param string $action
     * @return $this
     */
    public function onDelete(string $action): self
    {
        $this->command['onDelete'] = strtoupper($action);
        return $this;
    }

    /**
     * Set the ON UPDATE action
     *
     * @param string $action
     * @return $this
     */
    public function onUpdate(string $action): self
    {
        $this->command['onUpdate'] = strtoupper($action);
        return $this;
    }

    /**
     * Set ON DELETE CASCADE
     *
     * @return $this
     */
    public function cascadeOnDelete(): self
    {
        return $this->onDelete('CASCADE');
    }

    /**
     * Set ON UPDATE CASCADE
     *
     * @return $this
     */
    public function cascadeOnUpdate(): self
    {
        return $this->onUpdate('CASCADE');
    }

    /**
     * Set ON DELETE RESTRICT
     *
     * @return $this
     */
    public function restrictOnDelete(): self
    {
        return $this->onDelete('RESTRICT');
    }

    /**
     * Set ON UPDATE RESTRICT
     *
     * @return $this
     */
    public function restrictOnUpdate(): self
    {
        return $this->onUpdate('RESTRICT');
    }

    /**
     * Set ON DELETE SET NULL
     *
     * @return $this
     */
    public function nullOnDelete(): self
    {
        return $this->onDelete('SET NULL');
    }

    /**
     * Set ON DELETE NO ACTION
     *
     * @return $this
     */
    public function noActionOnDelete(): self
    {
        return $this->onDelete('NO ACTION');
    }

    /**
     * Set ON UPDATE NO ACTION
     *
     * @return $this
     */
    public function noActionOnUpdate(): self
    {
        return $this->onUpdate('NO ACTION');
    }

    /**
     * Set the constraint name
     *
     * @param string $name
     * @return $this
     */
    public function name(string $name): self
    {
        $this->command['name'] = $name;
        return $this;
    }

    /**
     * Mark the constraint as deferrable (PostgreSQL)
     *
     * @param bool $value
     * @return $this
     */
    public function deferrable(bool $value = true): self
    {
        $this->command['deferrable'] = $value;
        return $this;
    }

    /**
     * Set the constraint as initially deferred (PostgreSQL)
     *
     * @param bool $value
     * @return $this
     */
    public function initiallyDeferred(bool $value = true): self
    {
        $this->command['initiallyDeferred'] = $value;
        return $this;
    }
}
