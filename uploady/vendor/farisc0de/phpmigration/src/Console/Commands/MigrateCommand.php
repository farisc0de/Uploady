<?php

namespace Farisc0de\PhpMigration\Console\Commands;

use Farisc0de\PhpMigration\Console\Command;
use Farisc0de\PhpMigration\Migrations\Migrator;

/**
 * Class MigrateCommand
 * 
 * Run database migrations
 */
class MigrateCommand extends Command
{
    /**
     * The command name
     *
     * @var string
     */
    protected string $name = 'migrate';

    /**
     * The command description
     *
     * @var string
     */
    protected string $description = 'Run the database migrations';

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
        'step' => [
            'description' => 'Force the migrations to be run so they can be rolled back individually',
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
     * Create a new migrate command instance
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

        $migrations = $this->migrator->run([
            'pretend' => isset($options['pretend']),
            'step' => isset($options['step']),
        ]);

        if (empty($migrations)) {
            $this->info('Nothing to migrate.');
        } else {
            $this->info('Migration completed successfully.');
        }

        return 0;
    }
}
