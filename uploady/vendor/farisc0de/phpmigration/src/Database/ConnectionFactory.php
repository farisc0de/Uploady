<?php

namespace Farisc0de\PhpMigration\Database;

use Farisc0de\PhpMigration\Contracts\ConnectionInterface;
use Farisc0de\PhpMigration\Contracts\SchemaGrammarInterface;
use Farisc0de\PhpMigration\Schema\Grammars\MySqlGrammar;
use Farisc0de\PhpMigration\Schema\Grammars\PostgresGrammar;
use Farisc0de\PhpMigration\Schema\Grammars\SqliteGrammar;
use Farisc0de\PhpMigration\Support\Config;
use InvalidArgumentException;

/**
 * Class ConnectionFactory
 * 
 * Factory for creating database connections
 */
class ConnectionFactory
{
    /**
     * The configuration instance
     *
     * @var Config
     */
    protected Config $config;

    /**
     * The active connections
     *
     * @var array<string, ConnectionInterface>
     */
    protected array $connections = [];

    /**
     * Create a new connection factory instance
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Get a database connection
     *
     * @param string|null $name
     * @return ConnectionInterface
     */
    public function connection(?string $name = null): ConnectionInterface
    {
        $name = $name ?? $this->getDefaultConnection();

        if (!isset($this->connections[$name])) {
            $this->connections[$name] = $this->makeConnection($name);
        }

        return $this->connections[$name];
    }

    /**
     * Make a new database connection
     *
     * @param string $name
     * @return ConnectionInterface
     */
    protected function makeConnection(string $name): ConnectionInterface
    {
        $config = $this->getConnectionConfig($name);

        if (empty($config)) {
            throw new InvalidArgumentException("Database connection [{$name}] not configured.");
        }

        return Connection::create($config);
    }

    /**
     * Get the configuration for a connection
     *
     * @param string $name
     * @return array
     */
    protected function getConnectionConfig(string $name): array
    {
        return $this->config->get("database.connections.{$name}", []);
    }

    /**
     * Get the default connection name
     *
     * @return string
     */
    public function getDefaultConnection(): string
    {
        return $this->config->get('database.default', 'mysql');
    }

    /**
     * Set the default connection name
     *
     * @param string $name
     * @return void
     */
    public function setDefaultConnection(string $name): void
    {
        $this->config->set('database.default', $name);
    }

    /**
     * Get the grammar for a connection
     *
     * @param string|null $name
     * @return SchemaGrammarInterface
     */
    public function getGrammar(?string $name = null): SchemaGrammarInterface
    {
        $connection = $this->connection($name);
        $driver = $connection->getDriverName();

        return match ($driver) {
            'mysql' => new MySqlGrammar(),
            'pgsql' => new PostgresGrammar(),
            'sqlite' => new SqliteGrammar(),
            default => throw new InvalidArgumentException("Unsupported database driver: {$driver}"),
        };
    }

    /**
     * Disconnect from a connection
     *
     * @param string|null $name
     * @return void
     */
    public function disconnect(?string $name = null): void
    {
        $name = $name ?? $this->getDefaultConnection();
        unset($this->connections[$name]);
    }

    /**
     * Disconnect from all connections
     *
     * @return void
     */
    public function disconnectAll(): void
    {
        $this->connections = [];
    }

    /**
     * Get all active connections
     *
     * @return array<string, ConnectionInterface>
     */
    public function getConnections(): array
    {
        return $this->connections;
    }

    /**
     * Register a connection
     *
     * @param string $name
     * @param ConnectionInterface $connection
     * @return void
     */
    public function register(string $name, ConnectionInterface $connection): void
    {
        $this->connections[$name] = $connection;
    }

    /**
     * Check if a connection exists
     *
     * @param string $name
     * @return bool
     */
    public function hasConnection(string $name): bool
    {
        return isset($this->connections[$name]);
    }
}
