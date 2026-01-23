<?php

namespace Farisc0de\PhpMigration\Options;

/**
 * Class Types
 * 
 * Provides SQL data type definitions for database migrations
 */
class Types
{
    /**
     * Available MySQL column types
     */
    public const TYPE_VARCHAR = 'varchar';
    public const TYPE_INT = 'int';
    public const TYPE_TINYINT = 'tinyint';
    public const TYPE_TEXT = 'text';
    public const TYPE_LONGTEXT = 'longtext';
    public const TYPE_DATETIME = 'datetime';
    public const TYPE_TIMESTAMP = 'timestamp';
    public const TYPE_DECIMAL = 'decimal';
    public const TYPE_FLOAT = 'float';
    public const TYPE_DOUBLE = 'double';
    public const TYPE_DATE = 'date';
    public const TYPE_TIME = 'time';
    public const TYPE_YEAR = 'year';
    public const TYPE_CHAR = 'char';
    public const TYPE_BINARY = 'binary';
    public const TYPE_BLOB = 'blob';
    public const TYPE_ENUM = 'enum';

    /**
     * Create VARCHAR type with specified length
     *
     * @param int $length Maximum length of the string
     * @return string
     * @throws \InvalidArgumentException If length is not positive
     */
    public static function string(int $length): string
    {
        if ($length <= 0) {
            throw new \InvalidArgumentException('Length must be a positive integer');
        }
        return sprintf('%s(%d)', self::TYPE_VARCHAR, $length);
    }

    /**
     * Create INT type
     *
     * @param int|null $length Optional display width
     * @return string
     */
    public static function integer(?int $length = null): string
    {
        return $length ? sprintf('%s(%d)', self::TYPE_INT, $length) : self::TYPE_INT;
    }

    /**
     * Create TINYINT(1) type for boolean values
     *
     * @return string
     */
    public static function boolean(): string
    {
        return sprintf('%s(1)', self::TYPE_TINYINT);
    }

    /**
     * Create TEXT type
     *
     * @return string
     */
    public static function text(): string
    {
        return self::TYPE_TEXT;
    }

    /**
     * Create LONGTEXT type
     *
     * @return string
     */
    public static function longText(): string
    {
        return self::TYPE_LONGTEXT;
    }

    /**
     * Create DATETIME type
     *
     * @return string
     */
    public static function dateTime(): string
    {
        return self::TYPE_DATETIME;
    }

    /**
     * Create TIMESTAMP type
     *
     * @return string
     */
    public static function timeStamp(): string
    {
        return self::TYPE_TIMESTAMP;
    }

    /**
     * Create DECIMAL type
     *
     * @param int $precision Total number of digits
     * @param int $scale Number of decimal places
     * @return string
     * @throws \InvalidArgumentException If precision or scale is invalid
     */
    public static function decimal(int $precision, int $scale): string
    {
        if ($precision <= 0 || $scale < 0 || $scale > $precision) {
            throw new \InvalidArgumentException('Invalid precision or scale');
        }
        return sprintf('%s(%d,%d)', self::TYPE_DECIMAL, $precision, $scale);
    }

    /**
     * Create FLOAT type
     *
     * @return string
     */
    public static function float(): string
    {
        return self::TYPE_FLOAT;
    }

    /**
     * Create DOUBLE type
     *
     * @return string
     */
    public static function double(): string
    {
        return self::TYPE_DOUBLE;
    }

    /**
     * Create DATE type
     *
     * @return string
     */
    public static function date(): string
    {
        return self::TYPE_DATE;
    }

    /**
     * Create TIME type
     *
     * @return string
     */
    public static function time(): string
    {
        return self::TYPE_TIME;
    }

    /**
     * Create YEAR type
     *
     * @return string
     */
    public static function year(): string
    {
        return self::TYPE_YEAR;
    }

    /**
     * Create CHAR type
     *
     * @param int $length Fixed length
     * @return string
     * @throws \InvalidArgumentException If length is not positive
     */
    public static function char(int $length): string
    {
        if ($length <= 0) {
            throw new \InvalidArgumentException('Length must be a positive integer');
        }
        return sprintf('%s(%d)', self::TYPE_CHAR, $length);
    }

    /**
     * Create ENUM type
     *
     * @param array $values List of allowed values
     * @return string
     * @throws \InvalidArgumentException If values array is empty
     */
    public static function enum(array $values): string
    {
        if (empty($values)) {
            throw new \InvalidArgumentException('Enum values cannot be empty');
        }
        $quotedValues = array_map(fn($v) => "'$v'", $values);
        return sprintf('%s(%s)', self::TYPE_ENUM, implode(',', $quotedValues));
    }
}
