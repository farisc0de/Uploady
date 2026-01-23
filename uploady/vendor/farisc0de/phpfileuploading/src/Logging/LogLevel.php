<?php

namespace Farisc0de\PhpFileUploading\Logging;

/**
 * PSR-3 Log Levels
 *
 * @package PhpFileUploading
 */
class LogLevel
{
    public const EMERGENCY = 'emergency';
    public const ALERT = 'alert';
    public const CRITICAL = 'critical';
    public const ERROR = 'error';
    public const WARNING = 'warning';
    public const NOTICE = 'notice';
    public const INFO = 'info';
    public const DEBUG = 'debug';

    /**
     * Get all log levels in order of severity (highest first)
     */
    public static function all(): array
    {
        return [
            self::EMERGENCY,
            self::ALERT,
            self::CRITICAL,
            self::ERROR,
            self::WARNING,
            self::NOTICE,
            self::INFO,
            self::DEBUG,
        ];
    }

    /**
     * Check if a level is valid
     */
    public static function isValid(string $level): bool
    {
        return in_array($level, self::all(), true);
    }
}
