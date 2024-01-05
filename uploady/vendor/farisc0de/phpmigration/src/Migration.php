<?php

namespace Farisc0de\PhpMigration;

class Migration
{
    /**
     * Database Connection
     *
     * @var Database
     */
    private $db;
    /**
     * Utils Connection
     *
     * @var Utils
     */
    private $utils;

    /**
     * Update class constructor
     *
     * @param object $database
     *  An object from the Database class
     * @param object $utils
     *  An object from the Utils class
     * @return void
     */
    public function __construct($database, $utils)
    {
        $this->db = $database;

        $this->utils = $utils;
    }

    /**
     * Function to create a database in MySQL when needed
     *
     * @param string $database_name
     *  The database name you want to create in MySQL
     * @return bool
     *  Return true if the database is created otherwise return false
     */
    public function createDatabase($database_name)
    {
        $create_database_syntex = "CREATE DATABASE %s";

        $sql = $this->db->exec(sprintf($create_database_syntex, $database_name));

        return (is_int($sql) && $sql > 0);
    }

    /**
     * Create a new table in the database
     *
     * @param string $table_name
     *  The table name you want to create in the database
     * @param array $arrays
     *  An array contains the table columns as arrays
     * @return bool
     *  Return true if the table is created false otherwise
     */
    public function createTable($table_name, $columns)
    {
        $query = "";

        foreach ($columns as $column) {
            $query .= implode(" ", $column) . ", ";
        }

        $query = rtrim($query, ", ");

        $table_name = $this->utils->sanitize($table_name);

        $sql = sprintf("CREATE TABLE IF NOT EXISTS %s (%s);", $table_name, $query);

        $this->db->prepare($sql);

        return $this->db->execute();
    }

    /**
     * Modify a column to be primary
     *
     * @param string $table_name
     *  The table name that you want to modify
     * @param string $column_name
     *  The column name you want to make it primary
     * @return bool
     *  Return true if the column is modified otherwise false
     */
    public function setPrimary($table_name, $column_name)
    {

        $this->db->prepare(
            sprintf(
                "ALTER TABLE %s ADD PRIMARY KEY (%s);",
                $this->utils->sanitize($table_name),
                $this->utils->sanitize($column_name)
            )
        );

        return $this->db->execute();
    }

    /**
     * Modify a column to be auto-increment
     *
     * @param string $table_name
     *  The table name that you want to modify
     * @param array $column_array
     *  An array has the column you want to make auto-increment
     * @return bool
     *  Return true if the column is modified otherwise false
     */
    public function setAutoinc($table_name, $column_array)
    {
        $this->db->prepare(sprintf(
            "ALTER TABLE %s MODIFY %s AUTO_INCREMENT;",
            $this->utils->sanitize($table_name),
            implode(" ", $column_array)
        ));

        return $this->db->execute();
    }

    /**
     * Modify a column to be unique
     *
     * @param string $table_name
     *  The table name that you want to modify
     * @param array $column_name
     *  The column name you want to make unique
     * @return bool
     *  Return true if the column is modified otherwise false
     */
    public function setUnique($table_name, $column_name)
    {
        $this->db->prepare(sprintf(
            "ALTER TABLE %s ADD UNIQUE KEY %s (%s);",
            $this->utils->sanitize($table_name),
            $column_name,
            $column_name
        ));

        return $this->db->execute();
    }

    /**
     * Create a new column in a table in the database
     *
     * @param string $table_name
     *  The table name that you want to modify
     * @param array $array
     *  An array contains the new columns array
     * @param mixed $after
     *  If you want to add the new columns after a specific column [optional]
     * @return bool
     *  Return true if the column is added or false otherwise
     */
    public function createColumn($table_name, $column, $after = null)
    {
        $create_column_syntex = "ALTER TABLE %s ADD %s";

        $column = implode(" ", $column);

        $sql = sprintf($create_column_syntex, $table_name, $column);

        if ($after != null) {
            $sql .= " AFTER  " . $this->utils->sanitize($after);
        }

        $sql = $sql . ";";

        $this->db->prepare($sql);

        return $this->db->execute();
    }

    /**
     * Function to update a column data type when needed
     *
     * @param string $table_name
     *  The table name you want to modify
     * @param array $column_array
     *  The column array contains the name of the column you want to change its type
     * @return bool
     *  Return true if the type is changed otherwise return false
     */
    public function updateColumnType($table_name, $column_array)
    {
        $alter_syntax = "ALTER TABLE %s MODIFY COLUMN %s;";

        $sql = sprintf($alter_syntax, $table_name, implode(" ", $column_array));

        $this->db->prepare($sql);

        return $this->db->execute();
    }

    /**
     * Rename a table in a database
     *
     * @param string $oldTable
     *  The old table name you want to change
     * @param string $newTable
     *  The new table name you want
     * @return bool
     *  Return true if the name is updated false otherwise
     */
    public function renameTable($oldTable, $newTable)
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
     * @param string $table_name
     *  The table you want to insert data to
     * @param array $columns_array
     *  The column array should be an associative array that contains the column name as the key
     *
     *  Example: ["username" => "admin"]
     *
     * @return bool
     *  Return true if the value is inserted or false otherwise
     */
    public function insertValue($table_name, $columns_array)
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
     * @param string $table_name
     *  The table name you want to modify
     * @param string $column_name
     *  The column name you want to change its value
     * @param mixed $value
     *  The new value you want
     * @return bool
     *  Return true if the value is updated or false otherwise
     */
    public function updateValue($table_name, $column_name, $value)
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
     * @param string $table_name
     *  The table name you want to modify
     * @param string $column_name
     *  The column name you want to delete its value
     * @param mixed $value
     *  The value you want to delete
     * @return bool
     *  Return true if the value is deleted or false otherwise
     */
    public function deleteValue($table_name, $column_name, $value)
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
     * @param string $table_name
     *  The table name you want to remove
     * @return bool
     *  Return true if the table is removed otherwise false
     */
    public function dropTable($table_name)
    {
        $sql = sprintf("DROP TABLE %s;", $this->utils->sanitize($table_name));

        $this->db->prepare($sql);

        return $this->db->execute();
    }

    /**
     * Drop and remove a column from a table when needed
     *
     * @param string $table_name
     *  The table name you want to remove from the database
     * @param string $column_name
     *  The column name you want to remove
     * @return bool
     *  Return true if the column is removed otherwise return false
     */
    public function dropColumn($table_name, $column_name)
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
     * @param string $database_name
     *  The database name you want to remove completely
     * @return bool
     *  Return true if the database is removed otherwise false
     */
    public function dropDatabase($database_name)
    {
        $drop_db_syntax = "DROP DATABASE %s";

        $sql = sprintf($drop_db_syntax, $database_name);

        $this->db->prepare($sql);

        return $this->db->execute();
    }


    /**
     * Add INDEX to a column
     *
     * @param string $table_name
     *  The table name that you want to add the index to it
     * @param string $column
     *  the column inside the table you want add index to it
     * @return bool
     *  return true if the operation is a success of false otherwise
     */
    public function createIndex($table, $column)
    {
        $sql = sprintf("ALTER TABLE %s ADD INDEX(%s);", $table, $column);

        $this->db->prepare($sql);

        $this->db->execute();
    }
}
