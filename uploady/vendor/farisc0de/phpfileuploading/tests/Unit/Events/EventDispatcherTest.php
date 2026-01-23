<?php

namespace Farisc0de\PhpFileUploading\Tests\Unit\Events;

use PHPUnit\Framework\TestCase;
use Farisc0de\PhpFileUploading\Events\EventDispatcher;
use Farisc0de\PhpFileUploading\Events\FileEvent;
use Farisc0de\PhpFileUploading\Events\UploadEvents;

class EventDispatcherTest extends TestCase
{
    private EventDispatcher $dispatcher;

    protected function setUp(): void
    {
        $this->dispatcher = new EventDispatcher();
    }

    public function testAddListener(): void
    {
        $called = false;
        $this->dispatcher->addListener('test.event', function () use (&$called) {
            $called = true;
        });

        $this->assertTrue($this->dispatcher->hasListeners('test.event'));
    }

    public function testDispatchCallsListener(): void
    {
        $called = false;
        $this->dispatcher->addListener('test.event', function () use (&$called) {
            $called = true;
        });

        $event = new FileEvent('test.event');
        $this->dispatcher->dispatch($event);

        $this->assertTrue($called);
    }

    public function testDispatchPassesEventToListener(): void
    {
        $receivedEvent = null;
        $this->dispatcher->addListener('test.event', function ($event) use (&$receivedEvent) {
            $receivedEvent = $event;
        });

        $event = new FileEvent('test.event', null, ['key' => 'value']);
        $this->dispatcher->dispatch($event);

        $this->assertSame($event, $receivedEvent);
        $this->assertEquals('value', $receivedEvent->get('key'));
    }

    public function testListenerPriority(): void
    {
        $order = [];

        $this->dispatcher->addListener('test.event', function () use (&$order) {
            $order[] = 'low';
        }, -10);

        $this->dispatcher->addListener('test.event', function () use (&$order) {
            $order[] = 'high';
        }, 10);

        $this->dispatcher->addListener('test.event', function () use (&$order) {
            $order[] = 'normal';
        }, 0);

        $this->dispatcher->dispatch(new FileEvent('test.event'));

        $this->assertEquals(['high', 'normal', 'low'], $order);
    }

    public function testStopPropagation(): void
    {
        $calls = 0;

        $this->dispatcher->addListener('test.event', function ($event) use (&$calls) {
            $calls++;
            $event->stopPropagation();
        }, 10);

        $this->dispatcher->addListener('test.event', function () use (&$calls) {
            $calls++;
        }, 0);

        $this->dispatcher->dispatch(new FileEvent('test.event'));

        $this->assertEquals(1, $calls);
    }

    public function testRemoveListener(): void
    {
        $listener = function () {};
        $this->dispatcher->addListener('test.event', $listener);

        $this->assertTrue($this->dispatcher->hasListeners('test.event'));

        $this->dispatcher->removeListener('test.event', $listener);

        $this->assertFalse($this->dispatcher->hasListeners('test.event'));
    }

    public function testGetListeners(): void
    {
        $listener1 = function () {};
        $listener2 = function () {};

        $this->dispatcher->addListener('test.event', $listener1);
        $this->dispatcher->addListener('test.event', $listener2);

        $listeners = $this->dispatcher->getListeners('test.event');

        $this->assertCount(2, $listeners);
    }

    public function testClearListeners(): void
    {
        $this->dispatcher->addListener('test.event', function () {});
        $this->dispatcher->addListener('test.event', function () {});

        $this->dispatcher->clearListeners('test.event');

        $this->assertFalse($this->dispatcher->hasListeners('test.event'));
    }

    public function testClearAllListeners(): void
    {
        $this->dispatcher->addListener('event1', function () {});
        $this->dispatcher->addListener('event2', function () {});

        $this->dispatcher->clearAllListeners();

        $this->assertEmpty($this->dispatcher->getEventNames());
    }

    public function testSubscribe(): void
    {
        $called1 = false;
        $called2 = false;

        $this->dispatcher->subscribe([
            'event1' => function () use (&$called1) { $called1 = true; },
            'event2' => function () use (&$called2) { $called2 = true; },
        ]);

        $this->dispatcher->dispatch(new FileEvent('event1'));
        $this->dispatcher->dispatch(new FileEvent('event2'));

        $this->assertTrue($called1);
        $this->assertTrue($called2);
    }

    public function testSubscribeWithPriority(): void
    {
        $order = [];

        $this->dispatcher->subscribe([
            'test.event' => [
                'callback' => function () use (&$order) { $order[] = 'first'; },
                'priority' => 10,
            ],
        ]);

        $this->dispatcher->addListener('test.event', function () use (&$order) {
            $order[] = 'second';
        }, 0);

        $this->dispatcher->dispatch(new FileEvent('test.event'));

        $this->assertEquals(['first', 'second'], $order);
    }

    public function testGetEventNames(): void
    {
        $this->dispatcher->addListener('event1', function () {});
        $this->dispatcher->addListener('event2', function () {});
        $this->dispatcher->addListener('event3', function () {});

        $names = $this->dispatcher->getEventNames();

        $this->assertCount(3, $names);
        $this->assertContains('event1', $names);
        $this->assertContains('event2', $names);
        $this->assertContains('event3', $names);
    }

    public function testDispatchWithNoListeners(): void
    {
        $event = new FileEvent('no.listeners');
        $result = $this->dispatcher->dispatch($event);

        $this->assertSame($event, $result);
    }
}
