<?php

namespace Farisc0de\PhpFileUploading\Logging;

/**
 * PSR-3 compatible logger interface
 * 
 * This interface follows the PSR-3 specification for logging.
 * You can use any PSR-3 compatible logger (Monolog, etc.) by implementing this interface.
 *
 * @package PhpFileUploading
 */
interface LoggerInterface
{
    /**
     * System is unusable.
     */
    public function emergency(string|\Stringable $message, array $context = []): void;

    /**
     * Action must be taken immediately.
     */
    public function alert(string|\Stringable $message, array $context = []): void;

    /**
     * Critical conditions.
     */
    public function critical(string|\Stringable $message, array $context = []): void;

    /**
     * Runtime errors that do not require immediate action.
     */
    public function error(string|\Stringable $message, array $context = []): void;

    /**
     * Exceptional occurrences that are not errors.
     */
    public function warning(string|\Stringable $message, array $context = []): void;

    /**
     * Normal but significant events.
     */
    public function notice(string|\Stringable $message, array $context = []): void;

    /**
     * Interesting events.
     */
    public function info(string|\Stringable $message, array $context = []): void;

    /**
     * Detailed debug information.
     */
    public function debug(string|\Stringable $message, array $context = []): void;

    /**
     * Logs with an arbitrary level.
     */
    public function log(string $level, string|\Stringable $message, array $context = []): void;
}
