<?php

namespace Farisc0de\PhpMigration\Migrations;

use Farisc0de\PhpMigration\Contracts\MigrationInterface;
use Farisc0de\PhpMigration\Schema\SchemaBuilder;

/**
 * Class Migration
 * 
 * Base migration class that can be extended
 */
abstract class Migration implements MigrationInterface
{
    /**
     * The database connection name
     *
     * @var string|null
     */
    protected ?string $connection = null;

    /**
     * Enables wrapping the migration in a transaction (if supported)
     *
     * @var bool
     */
    public bool $withinTransaction = true;

    /**
     * Get the connection name for the migration
     *
     * @return string|null
     */
    public function getConnection(): ?string
    {
        return $this->connection;
    }

    /**
     * Run the migration
     *
     * @param SchemaBuilder $schema
     * @return void
     */
    abstract public function up(SchemaBuilder $schema): void;

    /**
     * Reverse the migration
     *
     * @param SchemaBuilder $schema
     * @return void
     */
    abstract public function down(SchemaBuilder $schema): void;
}
