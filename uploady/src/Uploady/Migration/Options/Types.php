<?php

namespace Uploady\Migration\Options;

class Types
{
    /**
     * String
     * 
     * @param mixed $length 
     * @return string 
     */
    public static function String($length)
    {
        return "varchar({$length})";
    }

    /**
     * Integer
     * 
     * @return string 
     */
    public static function Integer()
    {
        return "int";
    }

    /**
     * Boolean
     * 
     * @return string 
     */
    public static function Boolean()
    {
        return "tinyint(1)";
    }

    /**
     * Text
     * 
     * @return string 
     */
    public static function Text()
    {
        return "text";
    }

    /**
     * Long Text
     * 
     * @return string 
     */
    public static function LongText()
    {
        return "longtext";
    }

    /**
     * Date
     * 
     * @return string 
     */
    public static function DateTime()
    {
        return "datetime";
    }

    /**
     * Time Stamp
     * 
     * @return string 
     */
    public static function TimeStamp()
    {
        return "timestamp";
    }
}
