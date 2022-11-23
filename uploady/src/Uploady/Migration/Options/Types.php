<?php

namespace Uploady\Migration\Options;

class Types
{
    public static function String($length)
    {
        return "varchar({$length})";
    }

    public static function Integer()
    {
        return "int";
    }

    public static function Boolean()
    {
        return "tinyint(1)";
    }

    public static function Text()
    {
        return "text";
    }

    public static function LongText()
    {
        return "longtext";
    }

    public static function DateTime()
    {
        return "datetime";
    }

    public static function TimeStamp()
    {
        return "timestamp";
    }
}
