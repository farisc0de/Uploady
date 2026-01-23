<?php

namespace Farisc0de\PhpMigration\Tests\Unit\Support;

use PHPUnit\Framework\TestCase;
use Farisc0de\PhpMigration\Support\EventDispatcher;

class EventDispatcherTest extends TestCase
{
    public function testCanRegisterListener(): void
    {
        $dispatcher = new EventDispatcher();
        $dispatcher->listen('test.event', fn() => null);

        $this->assertTrue($dispatcher->hasListeners('test.event'));
    }

    public function testCanDispatchEvent(): void
    {
        $dispatcher = new EventDispatcher();
        $called = false;

        $dispatcher->listen('test.event', function () use (&$called) {
            $called = true;
        });

        $dispatcher->dispatch('test.event');

        $this->assertTrue($called);
    }

    public function testCanDispatchEventWithPayload(): void
    {
        $dispatcher = new EventDispatcher();
        $receivedPayload = null;

        $dispatcher->listen('test.event', function ($payload) use (&$receivedPayload) {
            $receivedPayload = $payload;
        });

        $dispatcher->dispatch('test.event', ['key' => 'value']);

        $this->assertEquals(['key' => 'value'], $receivedPayload);
    }

    public function testCanRegisterMultipleListeners(): void
    {
        $dispatcher = new EventDispatcher();
        $count = 0;

        $dispatcher->listen('test.event', function () use (&$count) {
            $count++;
        });

        $dispatcher->listen('test.event', function () use (&$count) {
            $count++;
        });

        $dispatcher->dispatch('test.event');

        $this->assertEquals(2, $count);
    }

    public function testCanForgetListeners(): void
    {
        $dispatcher = new EventDispatcher();
        $dispatcher->listen('test.event', fn() => null);

        $dispatcher->forget('test.event');

        $this->assertFalse($dispatcher->hasListeners('test.event'));
    }

    public function testCanForgetAllListeners(): void
    {
        $dispatcher = new EventDispatcher();
        $dispatcher->listen('event1', fn() => null);
        $dispatcher->listen('event2', fn() => null);

        $dispatcher->forget();

        $this->assertFalse($dispatcher->hasListeners('event1'));
        $this->assertFalse($dispatcher->hasListeners('event2'));
    }

    public function testWildcardListenerReceivesEventName(): void
    {
        $dispatcher = new EventDispatcher();
        $receivedEvent = null;

        $dispatcher->listen('*', function ($event) use (&$receivedEvent) {
            $receivedEvent = $event;
        });

        $dispatcher->dispatch('test.event');

        $this->assertEquals('test.event', $receivedEvent);
    }

    public function testDispatchingNonexistentEventDoesNothing(): void
    {
        $dispatcher = new EventDispatcher();

        // Should not throw
        $dispatcher->dispatch('nonexistent.event');

        $this->assertFalse($dispatcher->hasListeners('nonexistent.event'));
    }

    public function testCanGetListeners(): void
    {
        $dispatcher = new EventDispatcher();
        $listener = fn() => null;

        $dispatcher->listen('test.event', $listener);

        $listeners = $dispatcher->getListeners('test.event');

        $this->assertCount(1, $listeners);
    }
}
