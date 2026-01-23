<?php

namespace Farisc0de\PhpMigration\Database;

use Farisc0de\PhpMigration\Contracts\ConnectionInterface;
use PDO;
use PDOException;
use PDOStatement;

/**
 * Class Connection
 * 
 * Database connection wrapper implementing ConnectionInterface
 */
class Connection implements ConnectionInterface
{
    /**
     * The PDO connection
     *
     * @var PDO
     */
    protected PDO $pdo;

    /**
     * The current PDO statement
     *
     * @var PDOStatement|null
     */
    protected ?PDOStatement $stmt = null;

    /**
     * The database name
     *
     * @var string
     */
    protected string $database;

    /**
     * The driver name
     *
     * @var string
     */
    protected string $driver;

    /**
     * The table prefix
     *
     * @var string
     */
    protected string $tablePrefix = '';

    /**
     * The fetch mode
     *
     * @var int
     */
    protected int $fetchMode = PDO::FETCH_OBJ;

    /**
     * Create a new connection instance
     *
     * @param PDO $pdo
     * @param string $database
     * @param string $driver
     * @param string $tablePrefix
     */
    public function __construct(
        PDO $pdo,
        string $database,
        string $driver = 'mysql',
        string $tablePrefix = ''
    ) {
        $this->pdo = $pdo;
        $this->database = $database;
        $this->driver = $driver;
        $this->tablePrefix = $tablePrefix;
    }

    /**
     * Create a connection from configuration array
     *
     * @param array $config
     * @return static
     */
    public static function create(array $config): static
    {
        $driver = $config['driver'] ?? 'mysql';
        $host = $config['host'] ?? 'localhost';
        $port = $config['port'] ?? ($driver === 'mysql' ? 3306 : ($driver === 'pgsql' ? 5432 : null));
        $database = $config['database'] ?? $config['DB_NAME'] ?? '';
        $username = $config['username'] ?? $config['DB_USER'] ?? '';
        $password = $config['password'] ?? $config['DB_PASS'] ?? '';
        $charset = $config['charset'] ?? 'utf8mb4';
        $tablePrefix = $config['prefix'] ?? '';

        $dsn = match ($driver) {
            'mysql' => "mysql:host={$host};port={$port};dbname={$database};charset={$charset}",
            'pgsql' => "pgsql:host={$host};port={$port};dbname={$database}",
            'sqlite' => "sqlite:{$database}",
            default => throw new PDOException("Unsupported driver: {$driver}"),
        };

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => $config['fetch_mode'] ?? PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        if ($driver !== 'sqlite') {
            $options[PDO::ATTR_PERSISTENT] = $config['persistent'] ?? false;
        }

        $pdo = new PDO($dsn, $username, $password, $options);

        // Set charset for PostgreSQL
        if ($driver === 'pgsql') {
            $pdo->exec("SET NAMES '{$charset}'");
        }

        return new static($pdo, $database, $driver, $tablePrefix);
    }

    /**
     * Prepare a SQL statement
     *
     * @param string $query
     * @return void
     */
    public function prepare(string $query): void
    {
        $this->stmt = $this->pdo->prepare($query);
    }

    /**
     * Execute the prepared statement
     *
     * @return bool
     */
    public function execute(): bool
    {
        return $this->stmt->execute();
    }

    /**
     * Execute a query without results
     *
     * @param string $query
     * @return int|false
     */
    public function exec(string $query): int|false
    {
        return $this->pdo->exec($query);
    }

    /**
     * Bind a value to a parameter
     *
     * @param string $param
     * @param mixed $value
     * @param int|null $type
     * @return void
     */
    public function bind(string $param, mixed $value, ?int $type = null): void
    {
        if ($type === null) {
            $type = match (true) {
                is_int($value) => PDO::PARAM_INT,
                is_bool($value) => PDO::PARAM_BOOL,
                is_null($value) => PDO::PARAM_NULL,
                default => PDO::PARAM_STR,
            };
        }

        $this->stmt->bindValue($param, $value, $type);
    }

    /**
     * Get all results as an array
     *
     * @return array
     */
    public function resultset(): array
    {
        $this->execute();
        return $this->stmt->fetchAll($this->fetchMode);
    }

    /**
     * Get a single result
     *
     * @return object|array|false
     */
    public function single(): object|array|false
    {
        $this->execute();
        return $this->stmt->fetch($this->fetchMode);
    }

    /**
     * Get the row count
     *
     * @return int
     */
    public function rowCount(): int
    {
        return $this->stmt->rowCount();
    }

    /**
     * Get the last inserted ID
     *
     * @return string|false
     */
    public function lastInsertId(): string|false
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * Begin a transaction
     *
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit a transaction
     *
     * @return bool
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    /**
     * Rollback a transaction
     *
     * @return bool
     */
    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }

    /**
     * Check if connected
     *
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->pdo !== null;
    }

    /**
     * Get the database name
     *
     * @return string
     */
    public function getDatabaseName(): string
    {
        return $this->database;
    }

    /**
     * Get the driver name
     *
     * @return string
     */
    public function getDriverName(): string
    {
        return $this->driver;
    }

    /**
     * Get the table prefix
     *
     * @return string
     */
    public function getTablePrefix(): string
    {
        return $this->tablePrefix;
    }

    /**
     * Get the PDO instance
     *
     * @return PDO
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Set the fetch mode
     *
     * @param int $mode
     * @return void
     */
    public function setFetchMode(int $mode): void
    {
        $this->fetchMode = $mode;
    }

    /**
     * Run a select query
     *
     * @param string $query
     * @param array $bindings
     * @return array
     */
    public function select(string $query, array $bindings = []): array
    {
        $this->prepare($query);

        foreach ($bindings as $key => $value) {
            $param = is_int($key) ? $key + 1 : ":{$key}";
            $this->bind($param, $value);
        }

        return $this->resultset();
    }

    /**
     * Run an insert query
     *
     * @param string $query
     * @param array $bindings
     * @return bool
     */
    public function insert(string $query, array $bindings = []): bool
    {
        $this->prepare($query);

        foreach ($bindings as $key => $value) {
            $param = is_int($key) ? $key + 1 : ":{$key}";
            $this->bind($param, $value);
        }

        return $this->execute();
    }

    /**
     * Run an update query
     *
     * @param string $query
     * @param array $bindings
     * @return int
     */
    public function update(string $query, array $bindings = []): int
    {
        $this->prepare($query);

        foreach ($bindings as $key => $value) {
            $param = is_int($key) ? $key + 1 : ":{$key}";
            $this->bind($param, $value);
        }

        $this->execute();

        return $this->rowCount();
    }

    /**
     * Run a delete query
     *
     * @param string $query
     * @param array $bindings
     * @return int
     */
    public function delete(string $query, array $bindings = []): int
    {
        return $this->update($query, $bindings);
    }

    /**
     * Run a raw query
     *
     * @param string $query
     * @return bool
     */
    public function statement(string $query): bool
    {
        return $this->pdo->exec($query) !== false;
    }
}
