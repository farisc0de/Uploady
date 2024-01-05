<?php

namespace Farisc0de\PhpMigration\Options;

class Options
{
    /**
     * Not Null
     *
     * @return string
     */
    public static function notNull()
    {
        return "NOT NULL";
    }

    /**
     * Null
     *
     * @return string
     */
    public static function null()
    {
        return "NULL";
    }

    /**
     * Current Time Stamp
     *
     * @return string
     */
    public static function currentTimeStamp()
    {
        return "DEFAULT CURRENT_TIMESTAMP";
    }

    /**
     * Default Value
     *
     * @param string $value
     *  The value to be set as default
     * @return string
     *  The default value
     */
    public static function defaultValue($value)
    {
        if ($value == "CURRENT_TIMESTAMP") {
            return "DEFAULT CURRENT_TIMESTAMP";
        }

        if (is_string($value)) {
            return "DEFAULT '{$value}'";
        }

        if (is_bool($value)) {
            return "DEFAULT " . ($value ? 1 : 0);
        }

        if (is_null($value)) {
            return "DEFAULT NULL";
        }

        return "DEFAULT {$value}";
    }

    /**
     * Unsigned
     *
     * @return string
     */
    public static function unSigned()
    {
        return "UNSIGNED";
    }

    /**
     * Auto Increment
     *
     * @return string
     */
    public static function autoIncrement()
    {
        return "AUTO_INCREMENT";
    }

    /**
     * Unique
     *
     * @param string $column
     *  The column to be set as unique
     * @return string
     */
    public static function unique($column)
    {
        return "UNIQUE({$column})";
    }

    /**
     * Index
     *
     * @param string $column
     *  The column to be set as index
     * @return string
     */
    public static function index($column)
    {
        return "INDEX({$column})";
    }

    /**
     * Primary Key
     *
     * @param string $key
     *  The column to be set as primary key
     * @return string
     */
    public static function primaryKey($key)
    {
        return "PRIMARY KEY ({$key})";
    }

    /**
     * Foreign Key
     *
     * @param string $foreign_key
     *  The column to be set as foreign key
     * @param array $references
     *  The references to be set
     * @return string
     */
    public static function foreignKey($foreign_key, $references)
    {
        $ref = implode("", array_keys($references));
        $key = implode("", array_values($references));
        return "FOREIGN KEY ({$foreign_key}) REFERENCES {$ref}({$key}) ON DELETE CASCADE ON UPDATE CASCADE";
    }
}
