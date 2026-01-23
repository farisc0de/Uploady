<?php

namespace Farisc0de\PhpMigration\Contracts;

use Farisc0de\PhpMigration\Schema\Blueprint;

/**
 * Interface SchemaGrammarInterface
 * 
 * Defines the contract for database-specific SQL grammar
 */
interface SchemaGrammarInterface
{
    /**
     * Compile a create table command
     *
     * @param Blueprint $blueprint
     * @return array Array of SQL statements
     */
    public function compileCreate(Blueprint $blueprint): array;

    /**
     * Compile a drop table command
     *
     * @param string $tableName
     * @return string
     */
    public function compileDrop(string $tableName): string;

    /**
     * Compile a drop table if exists command
     *
     * @param string $tableName
     * @return string
     */
    public function compileDropIfExists(string $tableName): string;

    /**
     * Compile a rename table command
     *
     * @param string $from
     * @param string $to
     * @return string
     */
    public function compileRename(string $from, string $to): string;

    /**
     * Compile add column command
     *
     * @param Blueprint $blueprint
     * @return array
     */
    public function compileAdd(Blueprint $blueprint): array;

    /**
     * Compile modify column command
     *
     * @param Blueprint $blueprint
     * @return array
     */
    public function compileModify(Blueprint $blueprint): array;

    /**
     * Compile drop column command
     *
     * @param string $tableName
     * @param string|array $columns
     * @return string
     */
    public function compileDropColumn(string $tableName, string|array $columns): string;

    /**
     * Compile table exists query
     *
     * @return string
     */
    public function compileTableExists(): string;

    /**
     * Compile column listing query
     *
     * @return string
     */
    public function compileColumnListing(): string;

    /**
     * Get the driver name
     *
     * @return string
     */
    public function getDriverName(): string;
}
