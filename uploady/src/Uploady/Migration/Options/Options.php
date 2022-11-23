<?php

namespace Uploady\Migration\Options;

class Options
{
    public static function NotNull()
    {
        return "NOT NULL";
    }

    public static function Null()
    {
        return "NULL";
    }

    public static function CurrentTimeStamp()
    {
        return "DEFAULT CURRENT_TIMESTAMP";
    }

    public static function DefaultValue($value)
    {
        return "DEFAULT {$value}";
    }

    public static function UnSigned()
    {
        return "UNSIGNED";
    }

    public static function AutoIncrement()
    {
        return "AUTO_INCREMENT";
    }

    public static function Unique($column)
    {
        return "UNIQUE({$column})";
    }

    public static function Index($column)
    {
        return "INDEX({$column})";
    }

    public static function PrimaryKey($key)
    {
        return "PRIMARY KEY ({$key})";
    }

    public static function ForeignKey($foreign_key, $references)
    {
        $ref = implode("", array_keys($references));
        $key = implode("", array_values($references));
        return "FOREIGN KEY ({$foreign_key}) REFERENCES {$ref}({$key}) ON DELETE CASCADE ON UPDATE CASCADE";
    }
}
