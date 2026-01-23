<?php

namespace Farisc0de\PhpFileUploading\Events;

/**
 * Interface for all upload events
 *
 * @package PhpFileUploading
 */
interface EventInterface
{
    /**
     * Get the event name
     */
    public function getName(): string;

    /**
     * Get the event timestamp
     */
    public function getTimestamp(): float;

    /**
     * Check if propagation has been stopped
     */
    public function isPropagationStopped(): bool;

    /**
     * Stop event propagation
     */
    public function stopPropagation(): void;

    /**
     * Get event data
     */
    public function getData(): array;
}
