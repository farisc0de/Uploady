<?php

namespace Farisc0de\PhpMigration;

use Farisc0de\PhpMigration\Contracts\ConnectionInterface;

/**
 * Class Database
 * 
 * Legacy database connection class that implements ConnectionInterface
 * for backward compatibility with v1.x/v2.x API
 */
class Database implements ConnectionInterface
{
    /**
     * Database Host
     *
     * @var string
     */
    private string $host;

    /**
     * Database Username
     *
     * @var string
     */
    private string $user;

    /**
     * Database Password
     *
     * @var string
     */
    private string $pass;

    /**
     * Database Name
     *
     * @var string
     */
    private string $dbname;

    /**
     * Database Driver
     *
     * @var string
     */
    private string $driver = 'mysql';

    /**
     * Database Connection
     *
     * @var \PDO
     */
    private \PDO $connection;

    /**
     * Database Connection Error
     *
     * @var string|null
     */
    private ?string $error = null;

    /**
     * Database PDO Statement
     *
     * @var \PDOStatement|null
     */
    private ?\PDOStatement $stmt = null;

    /**
     * Check if the database is connected
     *
     * @var bool
     */
    private bool $dbconnected = false;

    /**
     * Controls the contents of the returned array
     *
     * @var int
     */
    private int $fetch_style = \PDO::FETCH_OBJ;

    /**
     * Database charset
     *
     * @var string
     */
    private string $charset = 'utf8mb4';

    /**
     * Database class constructor
     *
     * @param array $config Configuration array containing database settings
     * @throws \InvalidArgumentException If required configuration is missing
     */
    public function __construct(array $config)
    {
        if (!isset($config['DB_HOST'], $config['DB_USER'], $config['DB_PASS'], $config['DB_NAME'])) {
            throw new \InvalidArgumentException('Missing required database configuration parameters');
        }

        $this->host = $config['DB_HOST'];
        $this->user = $config['DB_USER'];
        $this->pass = $config['DB_PASS'];
        $this->dbname = $config['DB_NAME'];

        if (isset($config['DB_CHARSET'])) {
            $this->charset = $config['DB_CHARSET'];
        }

        if (isset($config['FETCH_STYLE'])) {
            $this->fetch_style = $config['FETCH_STYLE'];
        }

        $this->connect();
    }

    /**
     * Create a connection between PHP and a database server.
     *
     * @throws \PDOException When connection fails
     * @return void
     */
    private function connect()
    {
        $charset = $this->charset ?? 'utf8mb4';
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $this->host,
            $this->dbname,
            $charset
        );
        
        $options = [
            \PDO::ATTR_PERSISTENT => true,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => $this->fetch_style,
        ];

        try {
            $this->connection = new \PDO($dsn, $this->user, $this->pass, $options);
            $this->dbconnected = true;
        } catch (\PDOException $e) {
            $this->error = $e->getMessage();
            throw $e;
        }
    }

    /**
     * Get the Error Message
     *
     * @return string|null Returns the error message
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * Check if the class is connected to the database
     *
     * @return bool Returns true if connected
     */
    public function isConnected(): bool
    {
        return $this->dbconnected;
    }

    /**
     * Prepare the statement with SQL query
     *
     * @param string $query The SQL query you want to execute
     * @return void
     */
    public function prepare(string $query): void
    {
        $this->stmt = $this->connection->prepare($query);
    }

    /**
     * Prepare the statement with SQL query
     *
     * @param string $query
     *  The SQL query you want to execute
     * @return void
     */
    public function query($query)
    {
        $this->stmt = $this->connection->query($query);
    }

    /**
     * Execute the prepared statement
     *
     * @return bool Returns true if the query is executed successfully
     */
    public function execute(): bool
    {
        return $this->stmt->execute();
    }

    /**
     * Execute a query without results
     *
     * @param string $query The SQL query you want to execute
     * @return int|false Returns the number of rows affected
     */
    public function exec(string $query): int|false
    {
        return $this->connection->exec($query);
    }

    /**
     * Get the result set as an array of objects
     *
     * @return array Returns an array containing all rows in the result set
     */
    public function resultset(): array
    {
        $data = $this->stmt->fetchAll($this->fetch_style);
        return is_array($data) ? $data : [];
    }

    /**
     * Get the record row count
     *
     * @return int Returns the number of rows
     */
    public function rowCount(): int
    {
        return $this->stmt->rowCount();
    }

    /**
     * Get a single record as an object
     *
     * @return object|array|false Returns a single record or false
     */
    public function single(): object|array|false
    {
        return $this->stmt->fetch($this->fetch_style);
    }

    /**
     * Return the last inserted record id
     *
     * @return string|false Returns the last inserted ID
     */
    public function lastInsertId(): string|false
    {
        return $this->connection->lastInsertId();
    }

    /**
     * Bind the values with the PDO statement
     *
     * @param string $param The query parameter to bind
     * @param mixed $value The value to bind
     * @param int|null $type The PDO type (optional)
     * @return void
     */
    public function bind(string $param, mixed $value, ?int $type = null): void
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = \PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = \PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = \PDO::PARAM_NULL;
                    break;
                default:
                    $type = \PDO::PARAM_STR;
            }
        }

        $this->stmt->bindValue($param, $value, $type);
    }

    /**
     * Begin a transaction
     *
     * @return bool
     * @throws \PDOException
     */
    public function beginTransaction(): bool
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Commit a successful transaction
     *
     * @return bool
     * @throws \PDOException
     */
    public function commit(): bool
    {
        return $this->connection->commit();
    }

    /**
     * Rollback a failed transaction
     *
     * @return bool
     * @throws \PDOException
     */
    public function rollback(): bool
    {
        return $this->connection->rollBack();
    }

    /**
     * Return the Database Name (legacy method)
     *
     * @return string
     */
    public function returnDbName(): string
    {
        return $this->dbname;
    }

    /**
     * Get the database name (ConnectionInterface)
     *
     * @return string
     */
    public function getDatabaseName(): string
    {
        return $this->dbname;
    }

    /**
     * Get the driver name (ConnectionInterface)
     *
     * @return string
     */
    public function getDriverName(): string
    {
        return $this->driver;
    }

    /**
     * Get the PDO connection
     *
     * @return \PDO
     */
    public function getPdo(): \PDO
    {
        return $this->connection;
    }

    /**
     * Close the database connection
     *
     * @return void
     */
    public function __destruct()
    {
        unset($this->connection);
    }
}
