<?php

namespace Farisc0de\PhpMigration\Contracts;

/**
 * Interface SeederInterface
 * 
 * Defines the contract for seeder classes
 */
interface SeederInterface
{
    /**
     * Run the database seeds
     *
     * @return void
     */
    public function run(): void;
}
