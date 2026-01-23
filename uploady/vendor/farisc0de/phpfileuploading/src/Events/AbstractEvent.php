<?php

namespace Farisc0de\PhpFileUploading\Events;

/**
 * Base class for all upload events
 *
 * @package PhpFileUploading
 */
abstract class AbstractEvent implements EventInterface
{
    protected string $name;
    protected float $timestamp;
    protected bool $propagationStopped = false;
    protected array $data = [];

    public function __construct(string $name, array $data = [])
    {
        $this->name = $name;
        $this->timestamp = microtime(true);
        $this->data = $data;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTimestamp(): float
    {
        return $this->timestamp;
    }

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }

    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get a specific data value
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Set a data value
     */
    public function set(string $key, mixed $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }
}
