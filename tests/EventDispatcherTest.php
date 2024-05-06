<?php

namespace Dispify\EventDispatcher\Tests;

use Dispify\EventDispatcher\EventDispatcher;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class EventDispatcherTest extends TestCase
{
    public function testDispatch()
    {
        $event = new CustomEvent();
        $listener = $this->createPartialMock(CustomEvent::class, ['first', 'second']);

        $listener
            ->expects(self::at(0))
            ->method('first')
            ->with($event)
        ;

        $listener
            ->expects(self::at(1))
            ->method('second')
            ->with($event)
        ;

        ($listenerProvider = $this->createMock(ListenerProviderInterface::class))
            ->method('getListenersForEvent')
            ->with($event)
            ->willReturn([[$listener, 'first'], [$listener, 'second']])
        ;

        $eventDispatcher = new EventDispatcher($listenerProvider);

        self::assertSame($event, $eventDispatcher->dispatch($event));
    }

    public function testDispatchStoppableEvent()
    {
        $event = $this->createMock(StoppableEventInterface::class);
        $event->method('isPropagationStopped')->willReturn(true);

        $listener = $this->createPartialMock(CustomListener::class, ['__invoke']);
        $listener->expects(self::never())->method('__invoke');

        ($listenerProvider = $this->createMock(ListenerProviderInterface::class))
            ->method('getListenersForEvent')
            ->with($event)
            ->willReturn([$listener])
        ;

        $eventDispatcher = new EventDispatcher($listenerProvider);

        $eventDispatcher->dispatch($event);
    }
}
