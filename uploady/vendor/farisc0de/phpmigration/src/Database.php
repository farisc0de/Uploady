<?php

namespace Farisc0de\PhpMigration;

class Database
{
    /**
     * Database Host
     *
     * @var string
     */
    private $host;
    /**
     * Database Username
     *
     * @var string
     */
    private $user;
    /**
     * Database Password
     *
     * @var string
     */
    private $pass;
    /**
     * Database Name
     *
     * @var string
     */
    private $dbname;
    /**
     * Database Connection
     *
     * @var \PDO
     */
    private $connection;
    /**
     * Database Connection Error
     *
     * @var string
     */
    private $error;
    /**
     * Database PDO Statment
     *
     * @var \PDOStatement|bool
     */
    private $stmt;
    /**
     * Check if the database is connected
     *
     * @var bool
     */
    private $dbconnected = false;
    /**
     * Controls the contents of the returned array
     *
     * @var int
     */
    private $fetch_style = \PDO::FETCH_OBJ;
    /**
     * Database charset
     *
     * @var string
     */
    private $charset;

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
     * @return string
     *  Returns the error message generated from PDOException
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Check if the class is connected to the database
     *
     * @return bool
     *  Returns true if the class is connected to the database or false otherwise
     */
    public function isConnected()
    {
        return $this->dbconnected;
    }

    /**
     * Prepare the statement with SQL query
     *
     * @param string $query
     *  The SQL query you want to execute
     * @return void
     */
    public function prepare($query)
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
     * @return bool
     *  Returns true if the query is prepared query is executed successfully or false otherwise
     */
    public function execute()
    {
        return $this->stmt->execute();
    }

    /**
     * Execute a query without results
     *
     * @param mixed $query
     *  The SQL query you want to execute
     * @return mixed
     *  Returns the number of rows that were modified or deleted
     */
    public function exec($query)
    {
        return $this->connection->exec($query);
    }

    /**
     * Get the result set as an array of objects
     *
     * @return array
     *  Returns an array containing all of the remaining rows in the result set
     */
    public function resultset()
    {
        $data = $this->stmt->fetchAll($this->fetch_style);
        return is_array($data) ? $data : array();
    }

    /**
     * Get the record row count
     *
     * @return int
     *  Returns the number of rows
     */
    public function rowCount()
    {
        $data = $this->stmt->rowCount();
        return is_int($data) ? $data : 0;
    }

    /**
     * Get a single record as an object
     *
     * @return object|bool
     *  Returns an object that contains the information from a single record or false otherwise
     */
    public function single()
    {
        $data = $this->stmt->fetch($this->fetch_style);
        return is_object($data) ? $data : false;
    }

    /**
     * Return the last inserted record id
     *
     * @return mixed
     *  Returns the last row id that was inserted into the database
     */
    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }

    /**
     * Bind the values with the PDO statment
     *
     * @param string $param
     *  The query parameter you want bind to it
     * @param mixed $value
     *  The value you want to bind with the parameter
     * @param mixed $type
     *  The type of the parameter [optional]
     * @return void
     */
    public function bind($param, $value, $type = null)
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
    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Commit a successfull transaction
     *
     * @return bool
     * @throws \PDOException
     */
    public function commit()
    {
        return $this->connection->commit();
    }

    /**
     * Rollback a failed transaction
     *
     * @return bool
     * @throws \PDOException
     */
    public function rollback()
    {
        return $this->connection->rollBack();
    }

    /**
     * Return the Database Name
     *
     * @return void
     */
    public function returnDbName()
    {
        return $this->dbname;
    }

    /**
     * Close the database connection
     *
     * @return void
     */
    public function __destruct()
    {
        $this->connection = null;
    }
}
