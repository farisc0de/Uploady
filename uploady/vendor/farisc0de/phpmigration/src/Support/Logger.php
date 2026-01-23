<?php

namespace Farisc0de\PhpMigration\Support;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Class Logger
 * 
 * PSR-3 compatible logger implementation
 */
class Logger implements LoggerInterface
{
    /**
     * Log levels
     */
    protected const LEVELS = [
        LogLevel::EMERGENCY => 0,
        LogLevel::ALERT => 1,
        LogLevel::CRITICAL => 2,
        LogLevel::ERROR => 3,
        LogLevel::WARNING => 4,
        LogLevel::NOTICE => 5,
        LogLevel::INFO => 6,
        LogLevel::DEBUG => 7,
    ];

    /**
     * The minimum log level
     *
     * @var string
     */
    protected string $minLevel;

    /**
     * The log file path
     *
     * @var string|null
     */
    protected ?string $logFile;

    /**
     * Whether to output to console
     *
     * @var bool
     */
    protected bool $console;

    /**
     * The date format
     *
     * @var string
     */
    protected string $dateFormat = 'Y-m-d H:i:s';

    /**
     * Create a new logger instance
     *
     * @param string|null $logFile
     * @param string $minLevel
     * @param bool $console
     */
    public function __construct(
        ?string $logFile = null,
        string $minLevel = LogLevel::DEBUG,
        bool $console = false
    ) {
        $this->logFile = $logFile;
        $this->minLevel = $minLevel;
        $this->console = $console;
    }

    /**
     * System is unusable
     *
     * @param string|\Stringable $message
     * @param array $context
     * @return void
     */
    public function emergency(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately
     *
     * @param string|\Stringable $message
     * @param array $context
     * @return void
     */
    public function alert(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * Critical conditions
     *
     * @param string|\Stringable $message
     * @param array $context
     * @return void
     */
    public function critical(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors
     *
     * @param string|\Stringable $message
     * @param array $context
     * @return void
     */
    public function error(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors
     *
     * @param string|\Stringable $message
     * @param array $context
     * @return void
     */
    public function warning(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Normal but significant events
     *
     * @param string|\Stringable $message
     * @param array $context
     * @return void
     */
    public function notice(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Interesting events
     *
     * @param string|\Stringable $message
     * @param array $context
     * @return void
     */
    public function info(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Detailed debug information
     *
     * @param string|\Stringable $message
     * @param array $context
     * @return void
     */
    public function debug(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * Logs with an arbitrary level
     *
     * @param mixed $level
     * @param string|\Stringable $message
     * @param array $context
     * @return void
     */
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        if (!$this->shouldLog($level)) {
            return;
        }

        $message = $this->interpolate((string) $message, $context);
        $formatted = $this->formatMessage($level, $message, $context);

        if ($this->logFile) {
            $this->writeToFile($formatted);
        }

        if ($this->console) {
            $this->writeToConsole($level, $formatted);
        }
    }

    /**
     * Check if the level should be logged
     *
     * @param string $level
     * @return bool
     */
    protected function shouldLog(string $level): bool
    {
        return self::LEVELS[$level] <= self::LEVELS[$this->minLevel];
    }

    /**
     * Interpolate context values into the message
     *
     * @param string $message
     * @param array $context
     * @return string
     */
    protected function interpolate(string $message, array $context): string
    {
        $replace = [];

        foreach ($context as $key => $val) {
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        return strtr($message, $replace);
    }

    /**
     * Format the log message
     *
     * @param string $level
     * @param string $message
     * @param array $context
     * @return string
     */
    protected function formatMessage(string $level, string $message, array $context): string
    {
        $timestamp = date($this->dateFormat);
        $level = strtoupper($level);

        $formatted = "[{$timestamp}] [{$level}] {$message}";

        // Add exception info if present
        if (isset($context['exception']) && $context['exception'] instanceof \Throwable) {
            $exception = $context['exception'];
            $formatted .= "\nException: " . get_class($exception);
            $formatted .= "\nMessage: " . $exception->getMessage();
            $formatted .= "\nFile: " . $exception->getFile() . ':' . $exception->getLine();
            $formatted .= "\nTrace:\n" . $exception->getTraceAsString();
        }

        return $formatted;
    }

    /**
     * Write to the log file
     *
     * @param string $message
     * @return void
     */
    protected function writeToFile(string $message): void
    {
        $dir = dirname($this->logFile);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($this->logFile, $message . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    /**
     * Write to the console
     *
     * @param string $level
     * @param string $message
     * @return void
     */
    protected function writeToConsole(string $level, string $message): void
    {
        $colors = [
            LogLevel::EMERGENCY => "\033[41m", // Red background
            LogLevel::ALERT => "\033[41m",
            LogLevel::CRITICAL => "\033[31m", // Red
            LogLevel::ERROR => "\033[31m",
            LogLevel::WARNING => "\033[33m", // Yellow
            LogLevel::NOTICE => "\033[36m", // Cyan
            LogLevel::INFO => "\033[32m", // Green
            LogLevel::DEBUG => "\033[37m", // White
        ];

        $reset = "\033[0m";
        $color = $colors[$level] ?? '';

        $stream = in_array($level, [LogLevel::EMERGENCY, LogLevel::ALERT, LogLevel::CRITICAL, LogLevel::ERROR])
            ? STDERR
            : STDOUT;

        fwrite($stream, $color . $message . $reset . PHP_EOL);
    }

    /**
     * Set the log file path
     *
     * @param string $path
     * @return void
     */
    public function setLogFile(string $path): void
    {
        $this->logFile = $path;
    }

    /**
     * Set the minimum log level
     *
     * @param string $level
     * @return void
     */
    public function setMinLevel(string $level): void
    {
        $this->minLevel = $level;
    }

    /**
     * Enable or disable console output
     *
     * @param bool $enabled
     * @return void
     */
    public function setConsole(bool $enabled): void
    {
        $this->console = $enabled;
    }

    /**
     * Set the date format
     *
     * @param string $format
     * @return void
     */
    public function setDateFormat(string $format): void
    {
        $this->dateFormat = $format;
    }
}
