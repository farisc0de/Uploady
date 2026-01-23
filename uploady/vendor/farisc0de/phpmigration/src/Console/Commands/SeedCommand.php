<?php

namespace Farisc0de\PhpMigration\Console\Commands;

use Farisc0de\PhpMigration\Console\Command;
use Farisc0de\PhpMigration\Seeders\SeederManager;

/**
 * Class SeedCommand
 * 
 * Seed the database with records
 */
class SeedCommand extends Command
{
    /**
     * The command name
     *
     * @var string
     */
    protected string $name = 'db:seed';

    /**
     * The command description
     *
     * @var string
     */
    protected string $description = 'Seed the database with records';

    /**
     * The command arguments
     *
     * @var array
     */
    protected array $arguments = [
        'class' => [
            'description' => 'The class name of the seeder to run',
            'required' => false,
        ],
    ];

    /**
     * The command options
     *
     * @var array
     */
    protected array $options = [
        'path' => [
            'description' => 'The path to the seeders files',
            'default' => null,
        ],
        'force' => [
            'description' => 'Force the operation to run in production',
            'shortcut' => 'f',
        ],
    ];

    /**
     * The seeder manager instance
     *
     * @var SeederManager
     */
    protected SeederManager $manager;

    /**
     * Create a new seed command instance
     *
     * @param SeederManager $manager
     */
    public function __construct(SeederManager $manager)
    {
        $this->manager = $manager;
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
        $this->manager->setOutput(fn($msg) => $this->info($msg));

        $path = $options['path'] ?? null;
        if ($path) {
            $this->manager->path($path);
        }

        $class = $arguments['class'] ?? null;

        try {
            if ($class) {
                $this->manager->call($class);
            } else {
                $this->manager->runAll();
            }

            $this->info('Database seeding completed successfully.');
            return 0;
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }
    }
}
