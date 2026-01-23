<?php

namespace Farisc0de\PhpFileUploading\Logging;

/**
 * Trait for classes that need logging capabilities
 *
 * @package PhpFileUploading
 */
trait LoggerAwareTrait
{
    protected ?LoggerInterface $logger = null;

    /**
     * Set the logger instance
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * Get the logger instance
     */
    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Log a message if logger is available
     */
    protected function log(string $level, string $message, array $context = []): void
    {
        if ($this->logger !== null) {
            $this->logger->log($level, $message, $context);
        }
    }

    /**
     * Log debug message
     */
    protected function logDebug(string $message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * Log info message
     */
    protected function logInfo(string $message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Log warning message
     */
    protected function logWarning(string $message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Log error message
     */
    protected function logError(string $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }
}
