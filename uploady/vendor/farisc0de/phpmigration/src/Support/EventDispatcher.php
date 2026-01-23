<?php

namespace Farisc0de\PhpMigration\Support;

use Farisc0de\PhpMigration\Contracts\EventDispatcherInterface;

/**
 * Class EventDispatcher
 * 
 * Simple event dispatcher implementation
 */
class EventDispatcher implements EventDispatcherInterface
{
    /**
     * The registered event listeners
     *
     * @var array
     */
    protected array $listeners = [];

    /**
     * Register an event listener
     *
     * @param string $event The event name
     * @param callable $listener The listener callback
     * @return void
     */
    public function listen(string $event, callable $listener): void
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }

        $this->listeners[$event][] = $listener;
    }

    /**
     * Dispatch an event
     *
     * @param string $event The event name
     * @param array $payload The event payload
     * @return void
     */
    public function dispatch(string $event, array $payload = []): void
    {
        if (!isset($this->listeners[$event])) {
            return;
        }

        foreach ($this->listeners[$event] as $listener) {
            call_user_func($listener, $payload);
        }

        // Also dispatch wildcard listeners
        if (isset($this->listeners['*'])) {
            foreach ($this->listeners['*'] as $listener) {
                call_user_func($listener, $event, $payload);
            }
        }
    }

    /**
     * Remove all listeners for an event
     *
     * @param string|null $event The event name, or null to remove all
     * @return void
     */
    public function forget(?string $event = null): void
    {
        if ($event === null) {
            $this->listeners = [];
        } else {
            unset($this->listeners[$event]);
        }
    }

    /**
     * Check if an event has listeners
     *
     * @param string $event
     * @return bool
     */
    public function hasListeners(string $event): bool
    {
        return isset($this->listeners[$event]) && !empty($this->listeners[$event]);
    }

    /**
     * Get all listeners for an event
     *
     * @param string $event
     * @return array
     */
    public function getListeners(string $event): array
    {
        return $this->listeners[$event] ?? [];
    }
}
