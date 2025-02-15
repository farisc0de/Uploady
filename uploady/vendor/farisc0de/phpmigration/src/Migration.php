<?php

namespace Farisc0de\PhpMigration;

use InvalidArgumentException;
use PDOException;

class Migration
{
    /**
     * SQL syntax templates
     */
    private const SQL_CREATE_DATABASE = 'CREATE DATABASE %s';
    private const SQL_CREATE_TABLE = 'CREATE TABLE IF NOT EXISTS %s (%s)';
    private const SQL_ADD_PRIMARY_KEY = 'ALTER TABLE %s ADD PRIMARY KEY (%s)';
    private const SQL_ADD_AUTO_INCREMENT = 'ALTER TABLE %s MODIFY %s AUTO_INCREMENT';
    private const SQL_ADD_UNIQUE_KEY = 'ALTER TABLE %s ADD UNIQUE KEY %s (%s)';
    private const SQL_ADD_COLUMN = 'ALTER TABLE %s ADD %s';
    private const SQL_MODIFY_COLUMN = 'ALTER TABLE %s MODIFY COLUMN %s';

    /**
     * Database Connection
     *
     * @var Database
     */
    private Database $db;

    /**
     * Utils Connection
     *
     * @var Utils
     */
    private Utils $utils;

    /**
     * Migration class constructor
     *
     * @param Database $database Database connection instance
     * @param Utils $utils Utils instance
     */
    public function __construct(Database $database, Utils $utils)
    {
        $this->db = $database;
        $this->utils = $utils;
    }

    /**
     * Create a database in MySQL
     *
     * @param string $databaseName The database name to create
     * @throws InvalidArgumentException If database name is empty
     * @throws PDOException If database creation fails
     * @return bool True if database is created successfully
     */
    public function createDatabase(string $databaseName): bool
    {
        if (empty($databaseName)) {
            throw new InvalidArgumentException('Database name cannot be empty');
        }

        $sql = sprintf(self::SQL_CREATE_DATABASE, $this->utils->sanitize($databaseName));
        $result = $this->db->exec($sql);

        return (is_int($result) && $result >= 0);
    }

    /**
     * Create a new table in the database
     *
     * @param string $tableName The table name to create
     * @param array $columns Array of column definitions
     * @throws InvalidArgumentException If table name is empty or columns array is empty
     * @throws PDOException If table creation fails
     * @return bool True if table is created successfully
     */
    public function createTable(string $tableName, array $columns): bool
    {
        if (empty($tableName)) {
            throw new InvalidArgumentException('Table name cannot be empty');
        }

        if (empty($columns)) {
            throw new InvalidArgumentException('Columns array cannot be empty');
        }

        $columnDefinitions = array_map(
            fn($column) => implode(' ', $column),
            $columns
        );
        
        $query = implode(', ', $columnDefinitions);
        $sanitizedTableName = $this->utils->sanitize($tableName);
        
        $sql = sprintf(self::SQL_CREATE_TABLE, $sanitizedTableName, $query);
        
        $this->db->prepare($sql);
        return $this->db->execute();
    }

    /**
     * Set a column as primary key
     *
     * @param string $tableName The table to modify
     * @param string $columnName The column to make primary
     * @throws InvalidArgumentException If table or column name is empty
     * @throws PDOException If operation fails
     * @return bool True if operation succeeds
     */
    public function setPrimary(string $tableName, string $columnName): bool
    {
        if (empty($tableName) || empty($columnName)) {
            throw new InvalidArgumentException('Table name and column name cannot be empty');
        }

        $sql = sprintf(
            self::SQL_ADD_PRIMARY_KEY,
            $this->utils->sanitize($tableName),
            $this->utils->sanitize($columnName)
        );

        $this->db->prepare($sql);
        return $this->db->execute();
    }

    /**
     * Set a column as auto-increment
     *
     * @param string $tableName The table to modify
     * @param array $columnDefinition Column definition array
     * @throws InvalidArgumentException If table name or column definition is invalid
     * @throws PDOException If operation fails
     * @return bool True if operation succeeds
     */
    public function setAutoIncrement(string $tableName, array $columnDefinition): bool
    {
        if (empty($tableName) || empty($columnDefinition)) {
            throw new InvalidArgumentException('Table name and column definition cannot be empty');
        }

        $sql = sprintf(
            self::SQL_ADD_AUTO_INCREMENT,
            $this->utils->sanitize($tableName),
            implode(' ', $columnDefinition)
        );

        $this->db->prepare($sql);
        return $this->db->execute();
    }

    /**
     * Set a column as unique
     *
     * @param string $tableName The table to modify
     * @param string $columnName The column to make unique
     * @throws InvalidArgumentException If table or column name is empty
     * @throws PDOException If operation fails
     * @return bool True if operation succeeds
     */
    public function setUnique(string $tableName, string $columnName): bool
    {
        if (empty($tableName) || empty($columnName)) {
            throw new InvalidArgumentException('Table name and column name cannot be empty');
        }

        $sanitizedTableName = $this->utils->sanitize($tableName);
        $sanitizedColumnName = $this->utils->sanitize($columnName);

        $sql = sprintf(
            self::SQL_ADD_UNIQUE_KEY,
            $sanitizedTableName,
            $sanitizedColumnName,
            $sanitizedColumnName
        );

        $this->db->prepare($sql);
        return $this->db->execute();
    }

    /**
     * Add a new column to a table
     *
     * @param string $tableName The table to modify
     * @param array $columnDefinition Column definition array
     * @param string|null $after Column name to add after (optional)
     * @throws InvalidArgumentException If table name or column definition is invalid
     * @throws PDOException If operation fails
     * @return bool True if operation succeeds
     */
    public function addColumn(string $tableName, array $columnDefinition, ?string $after = null): bool
    {
        if (empty($tableName) || empty($columnDefinition)) {
            throw new InvalidArgumentException('Table name and column definition cannot be empty');
        }

        $columnStr = implode(' ', $columnDefinition);
        $sql = sprintf(self::SQL_ADD_COLUMN, $this->utils->sanitize($tableName), $columnStr);

        if ($after !== null) {
            $sql .= ' AFTER ' . $this->utils->sanitize($after);
        }

        $sql .= ';';

        $this->db->prepare($sql);
        return $this->db->execute();
    }

    /**
     * Modify a column's data type
     *
     * @param string $tableName The table to modify
     * @param array $columnDefinition Column definition array
     * @throws InvalidArgumentException If table name or column definition is invalid
     * @throws PDOException If operation fails
     * @return bool True if operation succeeds
     */
    public function modifyColumn(string $tableName, array $columnDefinition): bool
    {
        if (empty($tableName) || empty($columnDefinition)) {
            throw new InvalidArgumentException('Table name and column definition cannot be empty');
        }

        $sql = sprintf(
            self::SQL_MODIFY_COLUMN,
            $this->utils->sanitize($tableName),
            implode(' ', $columnDefinition)
        );

        $this->db->prepare($sql);
        return $this->db->execute();
    }

    /**
     * Rename a table in a database
     *
     * @param string $oldTable The old table name you want to change
     * @param string $newTable The new table name you want
     * @return bool Return true if the name is updated false otherwise
     */
    public function renameTable(string $oldTable, string $newTable): bool
    {
        $sql = sprintf(
            "ALTER TABLE %s RENAME TO %s;",
            $this->utils->sanitize($oldTable),
            $this->utils->sanitize($newTable)
        );

        $this->db->prepare($sql);

        return $this->db->execute();
    }

    /**
     * Insert a value to a column when needed
     *
     * @param string $table_name The table you want to insert data to
     * @param array $columns_array The column array should be an associative array that contains the column name as the key
     *
     *  Example: ["username" => "admin"]
     *
     * @return bool Return true if the value is inserted or false otherwise
     */
    public function insertValue(string $table_name, array $columns_array): bool
    {
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->utils->sanitize($table_name),
            implode(", ", array_keys($columns_array)),
            ":" . implode(",:", array_keys($columns_array))
        );

        $this->db->prepare($sql);

        foreach ($columns_array as $key => $value) {
            $this->db->bind(":" . $key, $value);
        }

        return $this->db->execute();
    }

    /**
     * Update a column value in a table
     *
     * @param string $table_name The table name you want to modify
     * @param string $column_name The column name you want to change its value
     * @param mixed $value The new value you want
     * @return bool Return true if the value is updated or false otherwise
     */
    public function updateValue(string $table_name, string $column_name, $value): bool
    {
        $sql = sprintf(
            "UPDATE %s SET %s = :value",
            $this->utils->sanitize($table_name),
            $this->utils->sanitize($column_name)
        );

        $this->db->prepare($sql);

        $this->db->bind(":value", $value);

        return $this->db->execute();
    }

    /**
     * Delete a value from a column in a table
     *
     * @param string $table_name The table name you want to modify
     * @param string $column_name The column name you want to delete its value
     * @param mixed $value The value you want to delete
     * @return bool Return true if the value is deleted or false otherwise
     */
    public function deleteValue(string $table_name, string $column_name, $value): bool
    {
        $sql = sprintf(
            "DELETE FROM %s WHERE %s = :value",
            $this->utils->sanitize($table_name),
            $this->utils->sanitize($column_name)
        );

        $this->db->prepare($sql);

        $this->db->bind(":value", $value);

        return $this->db->execute();
    }

    /**
     * Drop and remove a table from the database when needed
     *
     * @param string $table_name The table name you want to remove
     * @return bool Return true if the table is removed otherwise false
     */
    public function dropTable(string $table_name): bool
    {
        $sql = sprintf("DROP TABLE %s;", $this->utils->sanitize($table_name));

        $this->db->prepare($sql);

        return $this->db->execute();
    }

    /**
     * Drop and remove a column from a table when needed
     *
     * @param string $table_name The table name you want to remove from the database
     * @param string $column_name The column name you want to remove
     * @return bool Return true if the column is removed otherwise return false
     */
    public function dropColumn(string $table_name, string $column_name): bool
    {
        $sql = sprintf(
            "ALTER TABLE %s DROP COLUMN %s;",
            $this->utils->sanitize($table_name),
            $this->utils->sanitize($column_name)
        );

        $this->db->prepare($sql);

        return $this->db->execute();
    }

    /**
     * Drop and remove the database completely when needed
     *
     * @param string $database_name The database name you want to remove completely
     * @return bool Return true if the database is removed otherwise false
     */
    public function dropDatabase(string $database_name): bool
    {
        $drop_db_syntax = "DROP DATABASE %s";

        $sql = sprintf($drop_db_syntax, $database_name);

        $this->db->prepare($sql);

        return $this->db->execute();
    }

    /**
     * Add INDEX to a column
     *
     * @param string $table_name The table name that you want to add the index to it
     * @param string $column The column inside the table you want add index to it
     * @return bool Return true if the operation is a success of false otherwise
     */
    public function createIndex(string $table_name, string $column): bool
    {
        $sql = sprintf("ALTER TABLE %s ADD INDEX(%s);", $table_name, $column);

        $this->db->prepare($sql);

        $this->db->execute();

        return true;
    }
}
