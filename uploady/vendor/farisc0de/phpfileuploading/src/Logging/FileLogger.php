<?php

namespace Farisc0de\PhpFileUploading\Logging;

use RuntimeException;

/**
 * Simple file-based logger implementation
 *
 * @package PhpFileUploading
 */
class FileLogger implements LoggerInterface
{
    private string $logFile;
    private string $minLevel;
    private string $dateFormat;
    private array $levelPriority;

    public function __construct(
        string $logFile,
        string $minLevel = LogLevel::DEBUG,
        string $dateFormat = 'Y-m-d H:i:s'
    ) {
        $this->logFile = $logFile;
        $this->minLevel = $minLevel;
        $this->dateFormat = $dateFormat;
        $this->levelPriority = array_flip(LogLevel::all());

        $this->ensureLogFileExists();
    }

    public function emergency(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    public function log(string $level, string|\Stringable $message, array $context = []): void
    {
        if (!$this->shouldLog($level)) {
            return;
        }

        $message = $this->interpolate((string)$message, $context);
        $formattedMessage = $this->formatMessage($level, $message, $context);

        $this->write($formattedMessage);
    }

    /**
     * Check if the given level should be logged based on minimum level
     */
    private function shouldLog(string $level): bool
    {
        if (!isset($this->levelPriority[$level]) || !isset($this->levelPriority[$this->minLevel])) {
            return true;
        }

        return $this->levelPriority[$level] <= $this->levelPriority[$this->minLevel];
    }

    /**
     * Interpolate context values into the message placeholders
     */
    private function interpolate(string $message, array $context): string
    {
        $replace = [];
        foreach ($context as $key => $val) {
            if (is_string($val) || (is_object($val) && method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            } elseif (is_array($val)) {
                $replace['{' . $key . '}'] = json_encode($val);
            } elseif (is_bool($val)) {
                $replace['{' . $key . '}'] = $val ? 'true' : 'false';
            } elseif (is_null($val)) {
                $replace['{' . $key . '}'] = 'null';
            } elseif (is_scalar($val)) {
                $replace['{' . $key . '}'] = (string)$val;
            }
        }

        return strtr($message, $replace);
    }

    /**
     * Format the log message
     */
    private function formatMessage(string $level, string $message, array $context): string
    {
        $timestamp = date($this->dateFormat);
        $levelUpper = strtoupper($level);

        $logLine = "[{$timestamp}] [{$levelUpper}] {$message}";

        // Add context if not empty (excluding already interpolated values)
        $extraContext = array_filter($context, function ($key) use ($message) {
            return strpos($message, '{' . $key . '}') === false;
        }, ARRAY_FILTER_USE_KEY);

        if (!empty($extraContext)) {
            $logLine .= ' ' . json_encode($extraContext);
        }

        return $logLine . PHP_EOL;
    }

    /**
     * Write message to log file
     */
    private function write(string $message): void
    {
        $result = file_put_contents($this->logFile, $message, FILE_APPEND | LOCK_EX);

        if ($result === false) {
            throw new RuntimeException("Failed to write to log file: {$this->logFile}");
        }
    }

    /**
     * Ensure log file and directory exist
     */
    private function ensureLogFileExists(): void
    {
        $directory = dirname($this->logFile);

        if (!is_dir($directory)) {
            if (!mkdir($directory, 0755, true)) {
                throw new RuntimeException("Failed to create log directory: {$directory}");
            }
        }

        if (!file_exists($this->logFile)) {
            if (file_put_contents($this->logFile, '') === false) {
                throw new RuntimeException("Failed to create log file: {$this->logFile}");
            }
            chmod($this->logFile, 0644);
        }

        if (!is_writable($this->logFile)) {
            throw new RuntimeException("Log file is not writable: {$this->logFile}");
        }
    }

    /**
     * Get the log file path
     */
    public function getLogFile(): string
    {
        return $this->logFile;
    }

    /**
     * Clear the log file
     */
    public function clear(): void
    {
        if (file_put_contents($this->logFile, '') === false) {
            throw new RuntimeException("Failed to clear log file: {$this->logFile}");
        }
    }

    /**
     * Rotate log file if it exceeds the given size
     */
    public function rotate(int $maxSizeBytes = 10485760, int $maxFiles = 5): void
    {
        if (!file_exists($this->logFile)) {
            return;
        }

        $size = filesize($this->logFile);
        if ($size === false || $size < $maxSizeBytes) {
            return;
        }

        // Rotate existing files
        for ($i = $maxFiles - 1; $i >= 1; $i--) {
            $oldFile = $this->logFile . '.' . $i;
            $newFile = $this->logFile . '.' . ($i + 1);

            if (file_exists($oldFile)) {
                if ($i + 1 >= $maxFiles) {
                    unlink($oldFile);
                } else {
                    rename($oldFile, $newFile);
                }
            }
        }

        // Rotate current file
        rename($this->logFile, $this->logFile . '.1');

        // Create new empty log file
        file_put_contents($this->logFile, '');
        chmod($this->logFile, 0644);
    }
}
