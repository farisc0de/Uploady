<?php

namespace Farisc0de\PhpMigration\Console\Commands;

use Farisc0de\PhpMigration\Console\Command;
use Farisc0de\PhpMigration\Migrations\Migrator;

/**
 * Class RollbackCommand
 * 
 * Rollback database migrations
 */
class RollbackCommand extends Command
{
    /**
     * The command name
     *
     * @var string
     */
    protected string $name = 'migrate:rollback';

    /**
     * The command description
     *
     * @var string
     */
    protected string $description = 'Rollback the last database migration';

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
        'step' => [
            'description' => 'The number of migrations to be reverted',
            'shortcut' => 's',
            'default' => '1',
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
     * Create a new rollback command instance
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

        $step = (int) ($options['step'] ?? 1);

        $migrations = $this->migrator->rollback([
            'pretend' => isset($options['pretend']),
            'step' => $step,
        ]);

        if (empty($migrations)) {
            $this->info('Nothing to rollback.');
        } else {
            $this->info('Rollback completed successfully.');
        }

        return 0;
    }
}
