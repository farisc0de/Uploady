<?php

namespace Farisc0de\PhpMigration;

/**
 * Class Utils
 * 
 * Utility functions for database migrations
 */
class Utils
{
    /**
     * Characters that need to be escaped in SQL identifiers
     */
    private const UNSAFE_CHARS = '/[^a-zA-Z0-9_]/';

    /**
     * Maximum allowed length for database identifiers
     */
    private const MAX_IDENTIFIER_LENGTH = 64;

    /**
     * Sanitize a database identifier (table name, column name, etc.)
     *
     * @param string $identifier The database identifier to sanitize
     * @return string The sanitized identifier
     * @throws \InvalidArgumentException If identifier is empty or too long
     */
    public function sanitize(?string $identifier): string
    {
        if (empty($identifier)) {
            throw new \InvalidArgumentException('Identifier cannot be empty');
        }

        // Remove any unsafe characters
        $safe = preg_replace(self::UNSAFE_CHARS, '', $identifier);

        // Ensure the identifier isn't too long for MySQL
        if (strlen($safe) > self::MAX_IDENTIFIER_LENGTH) {
            throw new \InvalidArgumentException(
                sprintf('Identifier length cannot exceed %d characters', self::MAX_IDENTIFIER_LENGTH)
            );
        }

        return $safe;
    }

    /**
     * Escape a string value for use in SQL
     *
     * @param string|null $value The value to escape
     * @return string The escaped value
     */
    public function escapeString(?string $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        return "'" . addslashes($value) . "'";
    }

    /**
     * Format a timestamp for MySQL
     *
     * @param int|string|\DateTimeInterface|null $timestamp The timestamp to format
     * @return string Formatted timestamp
     * @throws \InvalidArgumentException If timestamp format is invalid
     */
    public function formatTimestamp(int|string|\DateTimeInterface|null $timestamp): string
    {
        if ($timestamp === null) {
            return 'NULL';
        }

        try {
            if (is_string($timestamp)) {
                $datetime = new \DateTime($timestamp);
            } elseif (is_int($timestamp)) {
                $datetime = new \DateTime();
                $datetime->setTimestamp($timestamp);
            } elseif ($timestamp instanceof \DateTimeInterface) {
                $datetime = $timestamp;
            } else {
                throw new \InvalidArgumentException('Invalid timestamp format');
            }

            return $datetime->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Invalid timestamp format: ' . $e->getMessage());
        }
    }

    /**
     * Convert a PHP value to its SQL representation
     *
     * @param mixed $value The value to convert
     * @return string SQL representation of the value
     */
    public function toSqlValue(mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_int($value) || is_float($value)) {
            return (string)$value;
        }

        if ($value instanceof \DateTimeInterface) {
            return "'" . $this->formatTimestamp($value) . "'";
        }

        if (is_array($value)) {
            $values = array_map([$this, 'escapeString'], $value);
            return '(' . implode(',', $values) . ')';
        }

        return $this->escapeString((string)$value);
    }

    /**
     * Generate a unique index/key name
     *
     * @param string $tableName The table name
     * @param string|array $columns The column(s) in the index
     * @param string $prefix The prefix for the index name (e.g., 'idx', 'fk', 'uk')
     * @return string The generated index name
     */
    public function generateIndexName(string $tableName, string|array $columns, string $prefix = 'idx'): string
    {
        $tableName = $this->sanitize($tableName);
        
        if (is_array($columns)) {
            $columnList = implode('_', array_map([$this, 'sanitize'], $columns));
        } else {
            $columnList = $this->sanitize($columns);
        }

        $indexName = sprintf('%s_%s_%s', $prefix, $tableName, $columnList);
        
        // Ensure the index name doesn't exceed MySQL's limit
        if (strlen($indexName) > self::MAX_IDENTIFIER_LENGTH) {
            $hash = substr(md5($indexName), 0, 8);
            $indexName = sprintf('%s_%s_%s', $prefix, substr($tableName, 0, 20), $hash);
        }

        return $indexName;
    }

    /**
     * Check if a string is a valid MySQL identifier
     *
     * @param string $identifier The identifier to check
     * @return bool True if valid, false otherwise
     */
    public function isValidIdentifier(string $identifier): bool
    {
        // Check length
        if (empty($identifier) || strlen($identifier) > self::MAX_IDENTIFIER_LENGTH) {
            return false;
        }

        // Check if it contains only allowed characters
        return preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $identifier) === 1;
    }
}
