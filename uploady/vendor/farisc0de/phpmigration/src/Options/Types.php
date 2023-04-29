<?php

namespace Farisc0de\PhpMigration\Options;

class Types
{
    /**
     * String
     *
     * @param mixed $length
     * @return string
     */
    public static function string($length)
    {
        return "varchar({$length})";
    }

    /**
     * Integer
     *
     * @return string
     */
    public static function integer()
    {
        return "int";
    }

    /**
     * Boolean
     *
     * @return string
     */
    public static function boolean()
    {
        return "tinyint(1)";
    }

    /**
     * Text
     *
     * @return string
     */
    public static function text()
    {
        return "text";
    }

    /**
     * Long Text
     *
     * @return string
     */
    public static function longText()
    {
        return "longtext";
    }

    /**
     * Date
     *
     * @return string
     */
    public static function dateTime()
    {
        return "datetime";
    }

    /**
     * Time Stamp
     *
     * @return string
     */
    public static function timeStamp()
    {
        return "timestamp";
    }
}
