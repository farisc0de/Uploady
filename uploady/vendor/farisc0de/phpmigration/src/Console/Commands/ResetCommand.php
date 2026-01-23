<?php

namespace Farisc0de\PhpMigration\Console\Commands;

use Farisc0de\PhpMigration\Console\Command;
use Farisc0de\PhpMigration\Migrations\Migrator;

/**
 * Class ResetCommand
 * 
 * Reset all database migrations
 */
class ResetCommand extends Command
{
    /**
     * The command name
     *
     * @var string
     */
    protected string $name = 'migrate:reset';

    /**
     * The command description
     *
     * @var string
     */
    protected string $description = 'Rollback all database migrations';

    /**
     * The command options
     *
     * @var array
     */
    protected array $options = [
        'path' => [
            'description' => 'The path to the migrations files',
            'default' => null,
        ],
        'pretend' => [
            'description' => 'Dump the SQL queries that would be run',
            'shortcut' => 'p',
        ],
        'force' => [
            'description' => 'Force the operation to run in production',
            'shortcut' => 'f',
        ],
    ];

    /**
     * The migrator instance
     *
     * @var Migrator
     */
    protected Migrator $migrator;

    /**
     * Create a new reset command instance
     *
     * @param Migrator $migrator
     */
    public function __construct(Migrator $migrator)
    {
        $this->migrator = $migrator;
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
        $this->migrator->setOutput(fn($msg) => $this->info($msg));

        $path = $options['path'] ?? null;
        if ($path) {
            $this->migrator->path($path);
        }

        $migrations = $this->migrator->reset([
            'pretend' => isset($options['pretend']),
        ]);

        if (empty($migrations)) {
            $this->info('Nothing to reset.');
        } else {
            $this->info('Reset completed successfully.');
        }

        return 0;
    }
}
