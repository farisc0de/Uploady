<?php

namespace Farisc0de\PhpMigration\Contracts;

/**
 * Interface MigrationInterface
 * 
 * Defines the contract for migration classes
 */
interface MigrationInterface
{
    /**
     * Run the migration
     *
     * @return void
     */
    public function up(): void;

    /**
     * Reverse the migration
     *
     * @return void
     */
    public function down(): void;
}
