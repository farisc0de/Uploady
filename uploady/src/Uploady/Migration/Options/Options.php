<?php

namespace Uploady\Migration\Options;

class Options
{
    /**
     * Not Null
     * 
     * @return string 
     */
    public static function NotNull()
    {
        return "NOT NULL";
    }

    /**
     * Null
     * 
     * @return string
     */
    public static function Null()
    {
        return "NULL";
    }

    /**
     * Current Time Stamp
     * 
     * @return string 
     */
    public static function CurrentTimeStamp()
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
    public static function DefaultValue($value)
    {
        return "DEFAULT {$value}";
    }

    /**
     * Unsigned
     * 
     * @return string
     */
    public static function UnSigned()
    {
        return "UNSIGNED";
    }

    /**
     * Auto Increment
     * 
     * @return string
     */
    public static function AutoIncrement()
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
    public static function Unique($column)
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
    public static function Index($column)
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
    public static function PrimaryKey($key)
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
    public static function ForeignKey($foreign_key, $references)
    {
        $ref = implode("", array_keys($references));
        $key = implode("", array_values($references));
        return "FOREIGN KEY ({$foreign_key}) REFERENCES {$ref}({$key}) ON DELETE CASCADE ON UPDATE CASCADE";
    }
}
