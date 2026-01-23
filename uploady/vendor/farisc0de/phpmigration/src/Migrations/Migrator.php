<?php

namespace Farisc0de\PhpMigration\Migrations;

use Farisc0de\PhpMigration\Contracts\ConnectionInterface;
use Farisc0de\PhpMigration\Contracts\MigrationInterface;
use Farisc0de\PhpMigration\Contracts\EventDispatcherInterface;
use Farisc0de\PhpMigration\Schema\SchemaBuilder;
use Farisc0de\PhpMigration\Contracts\SchemaGrammarInterface;
use RuntimeException;

/**
 * Class Migrator
 * 
 * Handles running and rolling back migrations
 */
class Migrator
{
    /**
     * The migration repository
     *
     * @var MigrationRepository
     */
    protected MigrationRepository $repository;

    /**
     * The database connection
     *
     * @var ConnectionInterface
     */
    protected ConnectionInterface $connection;

    /**
     * The schema builder
     *
     * @var SchemaBuilder
     */
    protected SchemaBuilder $schema;

    /**
     * The event dispatcher
     *
     * @var EventDispatcherInterface|null
     */
    protected ?EventDispatcherInterface $events;

    /**
     * The paths to all migration files
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
     * Create a new migrator instance
     *
     * @param MigrationRepository $repository
     * @param ConnectionInterface $connection
     * @param SchemaGrammarInterface $grammar
     * @param EventDispatcherInterface|null $events
     */
    public function __construct(
        MigrationRepository $repository,
        ConnectionInterface $connection,
        SchemaGrammarInterface $grammar,
        ?EventDispatcherInterface $events = null
    ) {
        $this->repository = $repository;
        $this->connection = $connection;
        $this->schema = new SchemaBuilder($connection, $grammar);
        $this->events = $events;
    }

    /**
     * Run the pending migrations
     *
     * @param array $options
     * @return array Array of migration files that were run
     */
    public function run(array $options = []): array
    {
        $this->ensureRepositoryExists();

        $files = $this->getMigrationFiles($this->paths);
        $ran = $this->repository->getRan();

        $migrations = array_diff(array_keys($files), $ran);

        $this->runMigrations($migrations, $files, $options);

        return $migrations;
    }

    /**
     * Run an array of migrations
     *
     * @param array $migrations
     * @param array $files
     * @param array $options
     * @return void
     */
    protected function runMigrations(array $migrations, array $files, array $options): void
    {
        if (empty($migrations)) {
            $this->note('Nothing to migrate.');
            return;
        }

        $batch = $this->repository->getNextBatchNumber();
        $pretend = $options['pretend'] ?? false;
        $step = $options['step'] ?? false;

        foreach ($migrations as $migration) {
            $this->runMigration($migration, $files[$migration], $batch, $pretend);

            if ($step) {
                $batch++;
            }
        }
    }

    /**
     * Run a single migration
     *
     * @param string $migration
     * @param string $path
     * @param int $batch
     * @param bool $pretend
     * @return void
     */
    protected function runMigration(string $migration, string $path, int $batch, bool $pretend): void
    {
        $this->note("Migrating: {$migration}");

        $startTime = microtime(true);

        $instance = $this->resolveMigration($path);

        $this->fireEvent('migrating', $migration);

        if ($pretend) {
            $this->note("Would run: {$migration}");
        } else {
            $this->runUp($instance);
            $this->repository->log($migration, $batch);
        }

        $this->fireEvent('migrated', $migration);

        $runTime = number_format((microtime(true) - $startTime) * 1000, 2);
        $this->note("Migrated: {$migration} ({$runTime}ms)");
    }

    /**
     * Run the "up" method on a migration
     *
     * @param MigrationInterface $migration
     * @return void
     */
    protected function runUp(MigrationInterface $migration): void
    {
        $this->connection->beginTransaction();

        try {
            $migration->up($this->schema);
            $this->connection->commit();
        } catch (\Throwable $e) {
            $this->connection->rollback();
            throw $e;
        }
    }

    /**
     * Rollback the last migration batch
     *
     * @param array $options
     * @return array Array of migration files that were rolled back
     */
    public function rollback(array $options = []): array
    {
        $this->ensureRepositoryExists();

        $steps = $options['step'] ?? 1;
        $pretend = $options['pretend'] ?? false;

        $migrations = $this->repository->getLast($steps);

        if (empty($migrations)) {
            $this->note('Nothing to rollback.');
            return [];
        }

        return $this->rollbackMigrations($migrations, $pretend);
    }

    /**
     * Rollback specific migrations
     *
     * @param array $migrations
     * @param bool $pretend
     * @return array
     */
    protected function rollbackMigrations(array $migrations, bool $pretend): array
    {
        $files = $this->getMigrationFiles($this->paths);
        $rolledBack = [];

        foreach ($migrations as $migration) {
            if (!isset($files[$migration])) {
                $this->note("Migration not found: {$migration}");
                continue;
            }

            $this->rollbackMigration($migration, $files[$migration], $pretend);
            $rolledBack[] = $migration;
        }

        return $rolledBack;
    }

    /**
     * Rollback a single migration
     *
     * @param string $migration
     * @param string $path
     * @param bool $pretend
     * @return void
     */
    protected function rollbackMigration(string $migration, string $path, bool $pretend): void
    {
        $this->note("Rolling back: {$migration}");

        $startTime = microtime(true);

        $instance = $this->resolveMigration($path);

        $this->fireEvent('rollingBack', $migration);

        if ($pretend) {
            $this->note("Would rollback: {$migration}");
        } else {
            $this->runDown($instance);
            $this->repository->delete($migration);
        }

        $this->fireEvent('rolledBack', $migration);

        $runTime = number_format((microtime(true) - $startTime) * 1000, 2);
        $this->note("Rolled back: {$migration} ({$runTime}ms)");
    }

    /**
     * Run the "down" method on a migration
     *
     * @param MigrationInterface $migration
     * @return void
     */
    protected function runDown(MigrationInterface $migration): void
    {
        $this->connection->beginTransaction();

        try {
            $migration->down($this->schema);
            $this->connection->commit();
        } catch (\Throwable $e) {
            $this->connection->rollback();
            throw $e;
        }
    }

    /**
     * Reset all migrations
     *
     * @param array $options
     * @return array
     */
    public function reset(array $options = []): array
    {
        $this->ensureRepositoryExists();

        $pretend = $options['pretend'] ?? false;
        $migrations = array_reverse($this->repository->getRan());

        if (empty($migrations)) {
            $this->note('Nothing to reset.');
            return [];
        }

        return $this->rollbackMigrations($migrations, $pretend);
    }

    /**
     * Refresh the database (reset and re-run all migrations)
     *
     * @param array $options
     * @return array
     */
    public function refresh(array $options = []): array
    {
        $this->reset($options);
        return $this->run($options);
    }

    /**
     * Get the status of all migrations
     *
     * @return array
     */
    public function status(): array
    {
        $this->ensureRepositoryExists();

        $ran = $this->repository->getRan();
        $files = $this->getMigrationFiles($this->paths);

        $status = [];

        foreach ($files as $name => $path) {
            $status[] = [
                'migration' => $name,
                'status' => in_array($name, $ran) ? 'Ran' : 'Pending',
            ];
        }

        return $status;
    }

    /**
     * Get the migration files from the given paths
     *
     * @param array $paths
     * @return array
     */
    public function getMigrationFiles(array $paths): array
    {
        $files = [];

        foreach ($paths as $path) {
            if (!is_dir($path)) {
                continue;
            }

            $migrationFiles = glob($path . '/*.php');

            foreach ($migrationFiles as $file) {
                $name = $this->getMigrationName($file);
                $files[$name] = $file;
            }
        }

        ksort($files);

        return $files;
    }

    /**
     * Get the migration name from the file path
     *
     * @param string $path
     * @return string
     */
    protected function getMigrationName(string $path): string
    {
        return str_replace('.php', '', basename($path));
    }

    /**
     * Resolve a migration instance from a file
     *
     * @param string $path
     * @return MigrationInterface
     */
    protected function resolveMigration(string $path): MigrationInterface
    {
        $migration = require $path;

        if (!$migration instanceof MigrationInterface) {
            throw new RuntimeException(
                "Migration file must return an instance of MigrationInterface: {$path}"
            );
        }

        return $migration;
    }

    /**
     * Ensure the migration repository exists
     *
     * @return void
     */
    protected function ensureRepositoryExists(): void
    {
        if (!$this->repository->repositoryExists()) {
            $this->repository->createRepository();
        }
    }

    /**
     * Add a path to the migration paths
     *
     * @param string $path
     * @return void
     */
    public function path(string $path): void
    {
        $this->paths[] = $path;
    }

    /**
     * Set the migration paths
     *
     * @param array $paths
     * @return void
     */
    public function setPaths(array $paths): void
    {
        $this->paths = $paths;
    }

    /**
     * Get the migration paths
     *
     * @return array
     */
    public function getPaths(): array
    {
        return $this->paths;
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
     * @param string $migration
     * @return void
     */
    protected function fireEvent(string $event, string $migration): void
    {
        if ($this->events) {
            $this->events->dispatch("migration.{$event}", [
                'migration' => $migration,
            ]);
        }
    }

    /**
     * Get the migration repository
     *
     * @return MigrationRepository
     */
    public function getRepository(): MigrationRepository
    {
        return $this->repository;
    }

    /**
     * Get the schema builder
     *
     * @return SchemaBuilder
     */
    public function getSchemaBuilder(): SchemaBuilder
    {
        return $this->schema;
    }

    /**
     * Get the database connection
     *
     * @return ConnectionInterface
     */
    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }
}
