<?php

namespace Farisc0de\PhpMigration\Console;

/**
 * Class Command
 * 
 * Base class for CLI commands
 */
abstract class Command
{
    /**
     * The command name
     *
     * @var string
     */
    protected string $name = '';

    /**
     * The command description
     *
     * @var string
     */
    protected string $description = '';

    /**
     * The command arguments
     *
     * @var array
     */
    protected array $arguments = [];

    /**
     * The command options
     *
     * @var array
     */
    protected array $options = [];

    /**
     * The application instance
     *
     * @var Application|null
     */
    protected ?Application $app = null;

    /**
     * Set the application instance
     *
     * @param Application $app
     * @return void
     */
    public function setApplication(Application $app): void
    {
        $this->app = $app;
    }

    /**
     * Get the command name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the command description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get the command arguments
     *
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Get the command options
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Get the command synopsis
     *
     * @return string
     */
    public function getSynopsis(): string
    {
        $synopsis = '';

        foreach ($this->arguments as $name => $definition) {
            $required = $definition['required'] ?? false;
            $synopsis .= $required ? "<{$name}> " : "[{$name}] ";
        }

        foreach ($this->options as $name => $definition) {
            $synopsis .= "[--{$name}] ";
        }

        return trim($synopsis);
    }

    /**
     * Execute the command
     *
     * @param array $arguments
     * @param array $options
     * @return int
     */
    abstract public function execute(array $arguments, array $options): int;

    /**
     * Write a line to output
     *
     * @param string $message
     * @return void
     */
    protected function line(string $message): void
    {
        echo $message . PHP_EOL;
    }

    /**
     * Write an info message
     *
     * @param string $message
     * @return void
     */
    protected function info(string $message): void
    {
        echo "\033[32m{$message}\033[0m" . PHP_EOL;
    }

    /**
     * Write a comment message
     *
     * @param string $message
     * @return void
     */
    protected function comment(string $message): void
    {
        echo "\033[33m{$message}\033[0m" . PHP_EOL;
    }

    /**
     * Write a warning message
     *
     * @param string $message
     * @return void
     */
    protected function warn(string $message): void
    {
        echo "\033[33mWarning: {$message}\033[0m" . PHP_EOL;
    }

    /**
     * Write an error message
     *
     * @param string $message
     * @return void
     */
    protected function error(string $message): void
    {
        fwrite(STDERR, "\033[31m{$message}\033[0m" . PHP_EOL);
    }

    /**
     * Write a table to output
     *
     * @param array $headers
     * @param array $rows
     * @return void
     */
    protected function table(array $headers, array $rows): void
    {
        // Calculate column widths
        $widths = [];
        foreach ($headers as $i => $header) {
            $widths[$i] = strlen($header);
        }

        foreach ($rows as $row) {
            foreach ($row as $i => $cell) {
                $widths[$i] = max($widths[$i] ?? 0, strlen((string) $cell));
            }
        }

        // Print header
        $this->printTableRow($headers, $widths, true);
        $this->printTableSeparator($widths);

        // Print rows
        foreach ($rows as $row) {
            $this->printTableRow($row, $widths);
        }
    }

    /**
     * Print a table row
     *
     * @param array $row
     * @param array $widths
     * @param bool $isHeader
     * @return void
     */
    protected function printTableRow(array $row, array $widths, bool $isHeader = false): void
    {
        $output = '| ';
        foreach ($row as $i => $cell) {
            $cell = str_pad((string) $cell, $widths[$i]);
            if ($isHeader) {
                $cell = "\033[1m{$cell}\033[0m";
            }
            $output .= $cell . ' | ';
        }
        $this->line($output);
    }

    /**
     * Print a table separator
     *
     * @param array $widths
     * @return void
     */
    protected function printTableSeparator(array $widths): void
    {
        $output = '+';
        foreach ($widths as $width) {
            $output .= str_repeat('-', $width + 2) . '+';
        }
        $this->line($output);
    }

    /**
     * Ask for confirmation
     *
     * @param string $question
     * @param bool $default
     * @return bool
     */
    protected function confirm(string $question, bool $default = false): bool
    {
        $defaultText = $default ? 'Y/n' : 'y/N';
        echo "{$question} [{$defaultText}]: ";

        $handle = fopen('php://stdin', 'r');
        $line = trim(fgets($handle));
        fclose($handle);

        if ($line === '') {
            return $default;
        }

        return strtolower($line) === 'y' || strtolower($line) === 'yes';
    }

    /**
     * Ask for input
     *
     * @param string $question
     * @param string|null $default
     * @return string
     */
    protected function ask(string $question, ?string $default = null): string
    {
        $defaultText = $default !== null ? " [{$default}]" : '';
        echo "{$question}{$defaultText}: ";

        $handle = fopen('php://stdin', 'r');
        $line = trim(fgets($handle));
        fclose($handle);

        return $line !== '' ? $line : ($default ?? '');
    }
}
