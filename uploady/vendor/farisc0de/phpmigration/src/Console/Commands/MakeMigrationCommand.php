<?php

namespace Farisc0de\PhpMigration\Console\Commands;

use Farisc0de\PhpMigration\Console\Command;
use Farisc0de\PhpMigration\Migrations\MigrationCreator;

/**
 * Class MakeMigrationCommand
 * 
 * Create a new migration file
 */
class MakeMigrationCommand extends Command
{
    /**
     * The command name
     *
     * @var string
     */
    protected string $name = 'make:migration';

    /**
     * The command description
     *
     * @var string
     */
    protected string $description = 'Create a new migration file';

    /**
     * The command arguments
     *
     * @var array
     */
    protected array $arguments = [
        'name' => [
            'description' => 'The name of the migration',
            'required' => true,
        ],
    ];

    /**
     * The command options
     *
     * @var array
     */
    protected array $options = [
        'create' => [
            'description' => 'The table to be created',
            'shortcut' => 'c',
        ],
        'table' => [
            'description' => 'The table to migrate',
            'shortcut' => 't',
        ],
        'path' => [
            'description' => 'The location where the migration file should be created',
            'default' => null,
        ],
    ];

    /**
     * The migration creator instance
     *
     * @var MigrationCreator
     */
    protected MigrationCreator $creator;

    /**
     * The default migration path
     *
     * @var string
     */
    protected string $defaultPath;

    /**
     * Create a new make migration command instance
     *
     * @param MigrationCreator $creator
     * @param string $defaultPath
     */
    public function __construct(MigrationCreator $creator, string $defaultPath = 'database/migrations')
    {
        $this->creator = $creator;
        $this->defaultPath = $defaultPath;
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
        $name = $arguments['name'] ?? null;

        if (!$name) {
            $this->error('Migration name is required.');
            return 1;
        }

        $table = $options['table'] ?? $options['create'] ?? null;
        $create = isset($options['create']);
        $path = $options['path'] ?? getcwd() . '/' . $this->defaultPath;

        try {
            $file = $this->creator->create($name, $path, $table, $create);
            $this->info("Created Migration: {$file}");
            return 0;
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }
    }
}
