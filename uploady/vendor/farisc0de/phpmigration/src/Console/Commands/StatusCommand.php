<?php

namespace Farisc0de\PhpMigration\Console\Commands;

use Farisc0de\PhpMigration\Console\Command;
use Farisc0de\PhpMigration\Migrations\Migrator;

/**
 * Class StatusCommand
 * 
 * Show the status of each migration
 */
class StatusCommand extends Command
{
    /**
     * The command name
     *
     * @var string
     */
    protected string $name = 'migrate:status';

    /**
     * The command description
     *
     * @var string
     */
    protected string $description = 'Show the status of each migration';

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
    ];

    /**
     * The migrator instance
     *
     * @var Migrator
     */
    protected Migrator $migrator;

    /**
     * Create a new status command instance
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
        $path = $options['path'] ?? null;
        if ($path) {
            $this->migrator->path($path);
        }

        $status = $this->migrator->status();

        if (empty($status)) {
            $this->info('No migrations found.');
            return 0;
        }

        $this->table(['Migration', 'Status'], array_map(function ($item) {
            $statusText = $item['status'] === 'Ran' 
                ? "\033[32m{$item['status']}\033[0m" 
                : "\033[33m{$item['status']}\033[0m";
            return [$item['migration'], $statusText];
        }, $status));

        return 0;
    }
}
