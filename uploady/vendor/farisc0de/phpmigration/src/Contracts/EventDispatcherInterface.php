<?php

namespace Farisc0de\PhpMigration\Contracts;

/**
 * Interface EventDispatcherInterface
 * 
 * Defines the contract for event dispatching
 */
interface EventDispatcherInterface
{
    /**
     * Register an event listener
     *
     * @param string $event The event name
     * @param callable $listener The listener callback
     * @return void
     */
    public function listen(string $event, callable $listener): void;

    /**
     * Dispatch an event
     *
     * @param string $event The event name
     * @param array $payload The event payload
     * @return void
     */
    public function dispatch(string $event, array $payload = []): void;

    /**
     * Remove all listeners for an event
     *
     * @param string|null $event The event name, or null to remove all
     * @return void
     */
    public function forget(?string $event = null): void;
}
