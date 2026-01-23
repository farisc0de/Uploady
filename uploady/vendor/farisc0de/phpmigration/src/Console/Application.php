<?php

namespace Farisc0de\PhpMigration\Console;

/**
 * Class Application
 * 
 * CLI Application for running migration commands
 */
class Application
{
    /**
     * The application name
     *
     * @var string
     */
    protected string $name = 'PhpMigration';

    /**
     * The application version
     *
     * @var string
     */
    protected string $version = '2.0.0';

    /**
     * The registered commands
     *
     * @var array<string, Command>
     */
    protected array $commands = [];

    /**
     * The default command
     *
     * @var string
     */
    protected string $defaultCommand = 'list';

    /**
     * Create a new application instance
     *
     * @param string|null $name
     * @param string|null $version
     */
    public function __construct(?string $name = null, ?string $version = null)
    {
        if ($name) {
            $this->name = $name;
        }
        if ($version) {
            $this->version = $version;
        }
    }

    /**
     * Register a command
     *
     * @param Command $command
     * @return void
     */
    public function add(Command $command): void
    {
        $command->setApplication($this);
        $this->commands[$command->getName()] = $command;
    }

    /**
     * Get a command by name
     *
     * @param string $name
     * @return Command|null
     */
    public function get(string $name): ?Command
    {
        return $this->commands[$name] ?? null;
    }

    /**
     * Get all commands
     *
     * @return array<string, Command>
     */
    public function all(): array
    {
        return $this->commands;
    }

    /**
     * Run the application
     *
     * @param array|null $argv
     * @return int
     */
    public function run(?array $argv = null): int
    {
        $argv = $argv ?? $_SERVER['argv'] ?? [];

        // Remove script name
        array_shift($argv);

        // Get command name
        $commandName = array_shift($argv) ?? $this->defaultCommand;

        // Handle help flag
        if ($commandName === '--help' || $commandName === '-h') {
            $this->showHelp();
            return 0;
        }

        // Handle version flag
        if ($commandName === '--version' || $commandName === '-v') {
            $this->showVersion();
            return 0;
        }

        // Handle list command
        if ($commandName === 'list') {
            $this->showHelp();
            return 0;
        }

        // Find and run command
        $command = $this->get($commandName);

        if (!$command) {
            $this->error("Command not found: {$commandName}");
            $this->showHelp();
            return 1;
        }

        // Parse arguments and options
        $input = $this->parseInput($argv, $command);

        // Check for help flag on command
        if (isset($input['options']['help']) || isset($input['options']['h'])) {
            $this->showCommandHelp($command);
            return 0;
        }

        try {
            return $command->execute($input['arguments'], $input['options']);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return 1;
        }
    }

    /**
     * Parse input arguments and options
     *
     * @param array $argv
     * @param Command $command
     * @return array
     */
    protected function parseInput(array $argv, Command $command): array
    {
        $arguments = [];
        $options = [];
        $argumentIndex = 0;
        $argumentDefinitions = $command->getArguments();
        $argumentNames = array_keys($argumentDefinitions);

        foreach ($argv as $arg) {
            if (str_starts_with($arg, '--')) {
                // Long option
                $arg = substr($arg, 2);
                if (str_contains($arg, '=')) {
                    [$key, $value] = explode('=', $arg, 2);
                    $options[$key] = $value;
                } else {
                    $options[$arg] = true;
                }
            } elseif (str_starts_with($arg, '-')) {
                // Short option
                $arg = substr($arg, 1);
                $options[$arg] = true;
            } else {
                // Argument
                if (isset($argumentNames[$argumentIndex])) {
                    $arguments[$argumentNames[$argumentIndex]] = $arg;
                    $argumentIndex++;
                }
            }
        }

        // Set default values for missing arguments
        foreach ($argumentDefinitions as $name => $definition) {
            if (!isset($arguments[$name]) && isset($definition['default'])) {
                $arguments[$name] = $definition['default'];
            }
        }

        // Set default values for missing options
        foreach ($command->getOptions() as $name => $definition) {
            if (!isset($options[$name]) && isset($definition['default'])) {
                $options[$name] = $definition['default'];
            }
        }

        return ['arguments' => $arguments, 'options' => $options];
    }

    /**
     * Show application help
     *
     * @return void
     */
    protected function showHelp(): void
    {
        $this->showVersion();
        $this->line('');
        $this->line("\033[33mUsage:\033[0m");
        $this->line('  command [options] [arguments]');
        $this->line('');
        $this->line("\033[33mAvailable commands:\033[0m");

        foreach ($this->commands as $name => $command) {
            $this->line(sprintf("  \033[32m%-20s\033[0m %s", $name, $command->getDescription()));
        }

        $this->line('');
        $this->line("\033[33mOptions:\033[0m");
        $this->line("  \033[32m-h, --help\033[0m     Display help for the given command");
        $this->line("  \033[32m-v, --version\033[0m  Display this application version");
    }

    /**
     * Show command help
     *
     * @param Command $command
     * @return void
     */
    protected function showCommandHelp(Command $command): void
    {
        $this->line("\033[33mDescription:\033[0m");
        $this->line('  ' . $command->getDescription());
        $this->line('');
        $this->line("\033[33mUsage:\033[0m");
        $this->line('  ' . $command->getName() . ' ' . $command->getSynopsis());
        $this->line('');

        $arguments = $command->getArguments();
        if (!empty($arguments)) {
            $this->line("\033[33mArguments:\033[0m");
            foreach ($arguments as $name => $definition) {
                $required = ($definition['required'] ?? false) ? '' : ' (optional)';
                $default = isset($definition['default']) ? " [default: {$definition['default']}]" : '';
                $this->line(sprintf("  \033[32m%-20s\033[0m %s%s%s", $name, $definition['description'] ?? '', $required, $default));
            }
            $this->line('');
        }

        $options = $command->getOptions();
        if (!empty($options)) {
            $this->line("\033[33mOptions:\033[0m");
            foreach ($options as $name => $definition) {
                $shortcut = isset($definition['shortcut']) ? "-{$definition['shortcut']}, " : '    ';
                $default = isset($definition['default']) ? " [default: {$definition['default']}]" : '';
                $this->line(sprintf("  \033[32m%s--%-15s\033[0m %s%s", $shortcut, $name, $definition['description'] ?? '', $default));
            }
        }
    }

    /**
     * Show application version
     *
     * @return void
     */
    protected function showVersion(): void
    {
        $this->line("\033[32m{$this->name}\033[0m version \033[33m{$this->version}\033[0m");
    }

    /**
     * Write a line to output
     *
     * @param string $message
     * @return void
     */
    public function line(string $message): void
    {
        echo $message . PHP_EOL;
    }

    /**
     * Write an info message
     *
     * @param string $message
     * @return void
     */
    public function info(string $message): void
    {
        echo "\033[32m{$message}\033[0m" . PHP_EOL;
    }

    /**
     * Write a warning message
     *
     * @param string $message
     * @return void
     */
    public function warn(string $message): void
    {
        echo "\033[33m{$message}\033[0m" . PHP_EOL;
    }

    /**
     * Write an error message
     *
     * @param string $message
     * @return void
     */
    public function error(string $message): void
    {
        fwrite(STDERR, "\033[31m{$message}\033[0m" . PHP_EOL);
    }

    /**
     * Get the application name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the application version
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }
}
