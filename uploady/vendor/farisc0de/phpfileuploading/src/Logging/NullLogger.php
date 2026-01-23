<?php

namespace Farisc0de\PhpFileUploading\Logging;

/**
 * Null logger that discards all log messages
 * 
 * Use this when you don't need logging functionality.
 *
 * @package PhpFileUploading
 */
class NullLogger implements LoggerInterface
{
    public function emergency(string|\Stringable $message, array $context = []): void
    {
    }

    public function alert(string|\Stringable $message, array $context = []): void
    {
    }

    public function critical(string|\Stringable $message, array $context = []): void
    {
    }

    public function error(string|\Stringable $message, array $context = []): void
    {
    }

    public function warning(string|\Stringable $message, array $context = []): void
    {
    }

    public function notice(string|\Stringable $message, array $context = []): void
    {
    }

    public function info(string|\Stringable $message, array $context = []): void
    {
    }

    public function debug(string|\Stringable $message, array $context = []): void
    {
    }

    public function log(string $level, string|\Stringable $message, array $context = []): void
    {
    }
}
