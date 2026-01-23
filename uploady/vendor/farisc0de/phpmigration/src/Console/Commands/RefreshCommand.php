<?php

namespace Farisc0de\PhpMigration\Console\Commands;

use Farisc0de\PhpMigration\Console\Command;
use Farisc0de\PhpMigration\Migrations\Migrator;

/**
 * Class RefreshCommand
 * 
 * Reset and re-run all migrations
 */
class RefreshCommand extends Command
{
    /**
     * The command name
     *
     * @var string
     */
    protected string $name = 'migrate:refresh';

    /**
     * The command description
     *
     * @var string
     */
    protected string $description = 'Reset and re-run all migrations';

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
        'seed' => [
            'description' => 'Run seeders after migrations',
            'shortcut' => 's',
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
     * Create a new refresh command instance
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

        $this->info('Refreshing database...');

        $migrations = $this->migrator->refresh();

        if (empty($migrations)) {
            $this->info('Nothing to refresh.');
        } else {
            $this->info('Refresh completed successfully.');
        }

        return 0;
    }
}
