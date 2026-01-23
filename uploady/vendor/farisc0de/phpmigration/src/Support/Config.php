<?php

namespace Farisc0de\PhpMigration\Support;

use InvalidArgumentException;

/**
 * Class Config
 * 
 * Configuration management with .env file support
 */
class Config
{
    /**
     * The configuration values
     *
     * @var array
     */
    protected array $config = [];

    /**
     * The environment variables
     *
     * @var array
     */
    protected array $env = [];

    /**
     * Create a new config instance
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Load configuration from a PHP file
     *
     * @param string $path
     * @return $this
     */
    public function loadFromFile(string $path): self
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentException("Config file not found: {$path}");
        }

        $config = require $path;

        if (!is_array($config)) {
            throw new InvalidArgumentException("Config file must return an array: {$path}");
        }

        $this->config = array_merge($this->config, $config);

        return $this;
    }

    /**
     * Load environment variables from a .env file
     *
     * @param string $path
     * @return $this
     */
    public function loadEnv(string $path): self
    {
        if (!file_exists($path)) {
            return $this;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Skip comments
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            // Parse key=value
            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Remove quotes
                $value = $this->parseEnvValue($value);

                $this->env[$key] = $value;

                // Also set in $_ENV and putenv
                $_ENV[$key] = $value;
                putenv("{$key}={$value}");
            }
        }

        return $this;
    }

    /**
     * Parse an environment value
     *
     * @param string $value
     * @return mixed
     */
    protected function parseEnvValue(string $value): mixed
    {
        // Remove surrounding quotes
        if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            $value = substr($value, 1, -1);
        }

        // Handle special values
        return match (strtolower($value)) {
            'true', '(true)' => true,
            'false', '(false)' => false,
            'null', '(null)' => null,
            'empty', '(empty)' => '',
            default => $value,
        };
    }

    /**
     * Get a configuration value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        // Check for dot notation
        if (str_contains($key, '.')) {
            return $this->getNestedValue($key, $default);
        }

        return $this->config[$key] ?? $default;
    }

    /**
     * Get a nested configuration value using dot notation
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getNestedValue(string $key, mixed $default): mixed
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * Set a configuration value
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        if (str_contains($key, '.')) {
            $this->setNestedValue($key, $value);
        } else {
            $this->config[$key] = $value;
        }
    }

    /**
     * Set a nested configuration value using dot notation
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    protected function setNestedValue(string $key, mixed $value): void
    {
        $keys = explode('.', $key);
        $config = &$this->config;

        foreach ($keys as $i => $segment) {
            if ($i === count($keys) - 1) {
                $config[$segment] = $value;
            } else {
                if (!isset($config[$segment]) || !is_array($config[$segment])) {
                    $config[$segment] = [];
                }
                $config = &$config[$segment];
            }
        }
    }

    /**
     * Check if a configuration key exists
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    /**
     * Get an environment variable
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function env(string $key, mixed $default = null): mixed
    {
        return $this->env[$key] ?? $_ENV[$key] ?? getenv($key) ?: $default;
    }

    /**
     * Get all configuration values
     *
     * @return array
     */
    public function all(): array
    {
        return $this->config;
    }

    /**
     * Merge configuration values
     *
     * @param array $config
     * @return void
     */
    public function merge(array $config): void
    {
        $this->config = array_merge_recursive($this->config, $config);
    }

    /**
     * Get database configuration
     *
     * @param string|null $connection
     * @return array
     */
    public function getDatabaseConfig(?string $connection = null): array
    {
        $connection = $connection ?? $this->get('database.default', 'mysql');
        $config = $this->get("database.connections.{$connection}", []);

        // Support environment variable substitution
        return array_map(function ($value) {
            if (is_string($value) && str_starts_with($value, '${') && str_ends_with($value, '}')) {
                $envKey = substr($value, 2, -1);
                return $this->env($envKey, $value);
            }
            return $value;
        }, $config);
    }
}
