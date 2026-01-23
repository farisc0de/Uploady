<?php

namespace Farisc0de\PhpFileUploading\Events;

/**
 * Interface for event listener classes
 *
 * @package PhpFileUploading
 */
interface ListenerInterface
{
    /**
     * Handle the event
     *
     * @param EventInterface $event The event to handle
     */
    public function handle(EventInterface $event): void;

    /**
     * Get the events this listener subscribes to
     *
     * @return array<string, int> Array of event names => priorities
     */
    public static function getSubscribedEvents(): array;
}
