<?php

namespace Farisc0de\PhpMigration\Console\Commands;

use Farisc0de\PhpMigration\Console\Command;
use Farisc0de\PhpMigration\Seeders\SeederCreator;

/**
 * Class MakeSeederCommand
 * 
 * Create a new seeder class
 */
class MakeSeederCommand extends Command
{
    /**
     * The command name
     *
     * @var string
     */
    protected string $name = 'make:seeder';

    /**
     * The command description
     *
     * @var string
     */
    protected string $description = 'Create a new seeder class';

    /**
     * The command arguments
     *
     * @var array
     */
    protected array $arguments = [
        'name' => [
            'description' => 'The name of the seeder',
            'required' => true,
        ],
    ];

    /**
     * The command options
     *
     * @var array
     */
    protected array $options = [
        'path' => [
            'description' => 'The location where the seeder file should be created',
            'default' => null,
        ],
    ];

    /**
     * The seeder creator instance
     *
     * @var SeederCreator
     */
    protected SeederCreator $creator;

    /**
     * The default seeder path
     *
     * @var string
     */
    protected string $defaultPath;

    /**
     * Create a new make seeder command instance
     *
     * @param SeederCreator $creator
     * @param string $defaultPath
     */
    public function __construct(SeederCreator $creator, string $defaultPath = 'database/seeders')
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
            $this->error('Seeder name is required.');
            return 1;
        }

        $path = $options['path'] ?? getcwd() . '/' . $this->defaultPath;

        try {
            $file = $this->creator->create($name, $path);
            $this->info("Created Seeder: {$file}");
            return 0;
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }
    }
}
