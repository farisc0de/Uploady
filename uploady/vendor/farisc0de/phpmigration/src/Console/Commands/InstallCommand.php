<?php

namespace Farisc0de\PhpMigration\Console\Commands;

use Farisc0de\PhpMigration\Console\Command;
use Farisc0de\PhpMigration\Migrations\MigrationRepository;

/**
 * Class InstallCommand
 * 
 * Create the migration repository
 */
class InstallCommand extends Command
{
    /**
     * The command name
     *
     * @var string
     */
    protected string $name = 'migrate:install';

    /**
     * The command description
     *
     * @var string
     */
    protected string $description = 'Create the migration repository';

    /**
     * The migration repository instance
     *
     * @var MigrationRepository
     */
    protected MigrationRepository $repository;

    /**
     * Create a new install command instance
     *
     * @param MigrationRepository $repository
     */
    public function __construct(MigrationRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Execute the command
     *
     * @param array $arguments
     * @param array $options
     * @return int
     */
    public function execute(array $arguments, array $options): int
    {
        if ($this->repository->repositoryExists()) {
            $this->info('Migration table already exists.');
            return 0;
        }

        $this->repository->createRepository();
        $this->info('Migration table created successfully.');

        return 0;
    }
}
