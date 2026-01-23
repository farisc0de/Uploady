<?php

namespace Farisc0de\PhpMigration\Seeders;

use Farisc0de\PhpMigration\Contracts\ConnectionInterface;
use Farisc0de\PhpMigration\Contracts\EventDispatcherInterface;
use RuntimeException;

/**
 * Class SeederManager
 * 
 * Manages database seeding operations
 */
class SeederManager
{
    /**
     * The database connection
     *
     * @var ConnectionInterface
     */
    protected ConnectionInterface $connection;

    /**
     * The event dispatcher
     *
     * @var EventDispatcherInterface|null
     */
    protected ?EventDispatcherInterface $events;

    /**
     * The paths to seeder files
     *
     * @var array
     */
    protected array $paths = [];

    /**
     * The output callback
     *
     * @var callable|null
     */
    protected $output = null;

    /**
     * Seeders that have been run
     *
     * @var array
     */
    protected array $ran = [];

    /**
     * Create a new seeder manager instance
     *
     * @param ConnectionInterface $connection
     * @param EventDispatcherInterface|null $events
     */
    public function __construct(
        ConnectionInterface $connection,
        ?EventDispatcherInterface $events = null
    ) {
        $this->connection = $connection;
        $this->events = $events;
    }

    /**
     * Run a seeder class
     *
     * @param string $class
     * @return void
     */
    public function call(string $class): void
    {
        $this->note("Seeding: {$class}");

        $startTime = microtime(true);

        $this->fireEvent('seeding', $class);

        $seeder = $this->resolve($class);
        $seeder->setConnection($this->connection);
        $seeder->setManager($this);
        $seeder->run();

        $this->ran[] = $class;

        $this->fireEvent('seeded', $class);

        $runTime = number_format((microtime(true) - $startTime) * 1000, 2);
        $this->note("Seeded: {$class} ({$runTime}ms)");
    }

    /**
     * Run multiple seeder classes
     *
     * @param array $classes
     * @return void
     */
    public function callMany(array $classes): void
    {
        foreach ($classes as $class) {
            $this->call($class);
        }
    }

    /**
     * Run all seeders in the paths
     *
     * @return void
     */
    public function runAll(): void
    {
        $seeders = $this->getSeederFiles();

        if (empty($seeders)) {
            $this->note('No seeders found.');
            return;
        }

        foreach ($seeders as $seeder) {
            $this->runSeederFile($seeder);
        }
    }

    /**
     * Run a seeder file
     *
     * @param string $path
     * @return void
     */
    protected function runSeederFile(string $path): void
    {
        $seeder = require $path;

        if ($seeder instanceof Seeder) {
            $className = get_class($seeder);
            $this->note("Seeding: {$className}");

            $startTime = microtime(true);

            $seeder->setConnection($this->connection);
            $seeder->setManager($this);
            $seeder->run();

            $runTime = number_format((microtime(true) - $startTime) * 1000, 2);
            $this->note("Seeded: {$className} ({$runTime}ms)");
        }
    }

    /**
     * Resolve a seeder class
     *
     * @param string $class
     * @return Seeder
     */
    protected function resolve(string $class): Seeder
    {
        if (!class_exists($class)) {
            // Try to find in paths
            $found = false;
            foreach ($this->paths as $path) {
                $file = $path . '/' . $class . '.php';
                if (file_exists($file)) {
                    require_once $file;
                    $found = true;
                    break;
                }
            }

            if (!$found && !class_exists($class)) {
                throw new RuntimeException("Seeder class not found: {$class}");
            }
        }

        $seeder = new $class();

        if (!$seeder instanceof Seeder) {
            throw new RuntimeException("Class must extend Seeder: {$class}");
        }

        return $seeder;
    }

    /**
     * Get all seeder files from paths
     *
     * @return array
     */
    public function getSeederFiles(): array
    {
        $files = [];

        foreach ($this->paths as $path) {
            if (!is_dir($path)) {
                continue;
            }

            $seederFiles = glob($path . '/*.php');
            $files = array_merge($files, $seederFiles);
        }

        sort($files);

        return $files;
    }

    /**
     * Add a path to the seeder paths
     *
     * @param string $path
     * @return void
     */
    public function path(string $path): void
    {
        $this->paths[] = $path;
    }

    /**
     * Set the seeder paths
     *
     * @param array $paths
     * @return void
     */
    public function setPaths(array $paths): void
    {
        $this->paths = $paths;
    }

    /**
     * Get the seeder paths
     *
     * @return array
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * Get the seeders that have been run
     *
     * @return array
     */
    public function getRan(): array
    {
        return $this->ran;
    }

    /**
     * Set the output callback
     *
     * @param callable $output
     * @return void
     */
    public function setOutput(callable $output): void
    {
        $this->output = $output;
    }

    /**
     * Write a note to the output
     *
     * @param string $message
     * @return void
     */
    protected function note(string $message): void
    {
        if ($this->output) {
            call_user_func($this->output, $message);
        }
    }

    /**
     * Fire an event
     *
     * @param string $event
     * @param string $seeder
     * @return void
     */
    protected function fireEvent(string $event, string $seeder): void
    {
        if ($this->events) {
            $this->events->dispatch("seeder.{$event}", [
                'seeder' => $seeder,
            ]);
        }
    }
}
