<?php

namespace Farisc0de\PhpMigration\Options;

/**
 * Class Options
 * 
 * Provides SQL column options and constraints for database migrations
 */
class Options
{
    public const DEFAULT_TIMESTAMP = 'CURRENT_TIMESTAMP';
    public const CASCADE = 'CASCADE';
    public const RESTRICT = 'RESTRICT';
    public const SET_NULL = 'SET NULL';
    public const NO_ACTION = 'NO ACTION';

    /**
     * Set column as NOT NULL
     *
     * @return string
     */
    public static function notNull(): string
    {
        return 'NOT NULL';
    }

    /**
     * Set column as NULL
     *
     * @return string
     */
    public static function null(): string
    {
        return 'NULL';
    }

    /**
     * Set default value as CURRENT_TIMESTAMP
     *
     * @return string
     */
    public static function currentTimeStamp(): string
    {
        return sprintf('DEFAULT %s', self::DEFAULT_TIMESTAMP);
    }

    /**
     * Set default value for a column
     *
     * @param mixed $value The value to be set as default
     * @return string The default value clause
     */
    public static function defaultValue(mixed $value): string
    {
        if ($value === self::DEFAULT_TIMESTAMP) {
            return self::currentTimeStamp();
        }

        if (is_string($value)) {
            return sprintf("DEFAULT '%s'", $value);
        }

        if (is_bool($value)) {
            return sprintf('DEFAULT %d', $value ? 1 : 0);
        }

        if (is_null($value)) {
            return 'DEFAULT NULL';
        }

        return sprintf('DEFAULT %s', $value);
    }

    /**
     * Set column as UNSIGNED
     *
     * @return string
     */
    public static function unsigned(): string
    {
        return 'UNSIGNED';
    }

    /**
     * Set column as AUTO_INCREMENT
     *
     * @return string
     */
    public static function autoIncrement(): string
    {
        return 'AUTO_INCREMENT';
    }

    /**
     * Create UNIQUE constraint
     *
     * @param string|array $columns One or more column names
     * @return string
     */
    public static function unique(string|array $columns): string
    {
        $cols = is_array($columns) ? implode(', ', $columns) : $columns;
        return sprintf('UNIQUE(%s)', $cols);
    }

    /**
     * Create INDEX
     *
     * @param string|array $columns One or more column names
     * @param string|null $name Optional index name
     * @return string
     */
    public static function index(string|array $columns, ?string $name = null): string
    {
        $cols = is_array($columns) ? implode(', ', $columns) : $columns;
        if ($name) {
            return sprintf('INDEX %s (%s)', $name, $cols);
        }
        return sprintf('INDEX(%s)', $cols);
    }

    /**
     * Create PRIMARY KEY constraint
     *
     * @param string|array $columns One or more column names
     * @return string
     */
    public static function primaryKey(string|array $columns): string
    {
        $cols = is_array($columns) ? implode(', ', $columns) : $columns;
        return sprintf('PRIMARY KEY (%s)', $cols);
    }

    /**
     * Create FOREIGN KEY constraint
     *
     * @param string $column The column to be set as foreign key
     * @param string $referenceTable The referenced table
     * @param string $referenceColumn The referenced column
     * @param string $onDelete ON DELETE behavior (CASCADE, RESTRICT, SET NULL, NO ACTION)
     * @param string $onUpdate ON UPDATE behavior (CASCADE, RESTRICT, SET NULL, NO ACTION)
     * @return string
     * @throws \InvalidArgumentException If invalid ON DELETE or ON UPDATE option is provided
     */
    public static function foreignKey(
        string $column,
        string $referenceTable,
        string $referenceColumn,
        string $onDelete = self::CASCADE,
        string $onUpdate = self::CASCADE
    ): string {
        $validActions = [self::CASCADE, self::RESTRICT, self::SET_NULL, self::NO_ACTION];
        
        if (!in_array($onDelete, $validActions) || !in_array($onUpdate, $validActions)) {
            throw new \InvalidArgumentException('Invalid ON DELETE or ON UPDATE option');
        }

        return sprintf(
            'FOREIGN KEY (%s) REFERENCES %s(%s) ON DELETE %s ON UPDATE %s',
            $column,
            $referenceTable,
            $referenceColumn,
            $onDelete,
            $onUpdate
        );
    }
}
