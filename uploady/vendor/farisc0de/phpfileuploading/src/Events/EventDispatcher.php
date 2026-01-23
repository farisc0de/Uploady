<?php

namespace Farisc0de\PhpFileUploading\Events;

use Farisc0de\PhpFileUploading\Logging\LoggerAwareTrait;

/**
 * Event dispatcher for upload events
 *
 * @package PhpFileUploading
 */
class EventDispatcher
{
    use LoggerAwareTrait;

    /** @var array<string, array<int, callable>> */
    private array $listeners = [];

    /**
     * Add an event listener
     *
     * @param string $eventName The event name to listen for
     * @param callable $listener The listener callback
     * @param int $priority Higher priority listeners are called first (default: 0)
     */
    public function addListener(string $eventName, callable $listener, int $priority = 0): self
    {
        if (!isset($this->listeners[$eventName])) {
            $this->listeners[$eventName] = [];
        }

        $this->listeners[$eventName][] = [
            'callback' => $listener,
            'priority' => $priority,
        ];

        // Sort by priority (higher first)
        usort($this->listeners[$eventName], function ($a, $b) {
            return $b['priority'] <=> $a['priority'];
        });

        return $this;
    }

    /**
     * Remove an event listener
     *
     * @param string $eventName The event name
     * @param callable $listener The listener to remove
     */
    public function removeListener(string $eventName, callable $listener): self
    {
        if (!isset($this->listeners[$eventName])) {
            return $this;
        }

        $this->listeners[$eventName] = array_filter(
            $this->listeners[$eventName],
            fn($item) => $item['callback'] !== $listener
        );

        return $this;
    }

    /**
     * Check if an event has listeners
     */
    public function hasListeners(string $eventName): bool
    {
        return !empty($this->listeners[$eventName]);
    }

    /**
     * Get all listeners for an event
     *
     * @return callable[]
     */
    public function getListeners(string $eventName): array
    {
        if (!isset($this->listeners[$eventName])) {
            return [];
        }

        return array_map(fn($item) => $item['callback'], $this->listeners[$eventName]);
    }

    /**
     * Dispatch an event to all registered listeners
     *
     * @param EventInterface $event The event to dispatch
     * @return EventInterface The event (possibly modified by listeners)
     */
    public function dispatch(EventInterface $event): EventInterface
    {
        $eventName = $event->getName();

        $this->logDebug('Dispatching event: {event}', ['event' => $eventName]);

        if (!isset($this->listeners[$eventName])) {
            return $event;
        }

        foreach ($this->listeners[$eventName] as $item) {
            if ($event->isPropagationStopped()) {
                $this->logDebug('Event propagation stopped for: {event}', ['event' => $eventName]);
                break;
            }

            try {
                $item['callback']($event);
            } catch (\Throwable $e) {
                $this->logError('Error in event listener for {event}: {error}', [
                    'event' => $eventName,
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }
        }

        return $event;
    }

    /**
     * Subscribe multiple events at once
     *
     * @param array<string, callable|array> $subscriptions Array of event => listener pairs
     */
    public function subscribe(array $subscriptions): self
    {
        foreach ($subscriptions as $eventName => $listener) {
            if (is_array($listener) && isset($listener['callback'])) {
                $this->addListener(
                    $eventName,
                    $listener['callback'],
                    $listener['priority'] ?? 0
                );
            } else {
                $this->addListener($eventName, $listener);
            }
        }

        return $this;
    }

    /**
     * Remove all listeners for an event
     */
    public function clearListeners(string $eventName): self
    {
        unset($this->listeners[$eventName]);
        return $this;
    }

    /**
     * Remove all listeners
     */
    public function clearAllListeners(): self
    {
        $this->listeners = [];
        return $this;
    }

    /**
     * Get all registered event names
     */
    public function getEventNames(): array
    {
        return array_keys($this->listeners);
    }
}
