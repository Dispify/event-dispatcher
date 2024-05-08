<?php

namespace Dispify\EventDispatcher\Tests;

use Dispify\EventDispatcher\EventDispatcher;
use Dispify\EventDispatcher\ListenerChainableProvider;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class EventListenerProviderTest extends TestCase
{
    public function testGetListenersForEvent()
    {
        $event = new CustomEvent();

        $listener = $this->createPartialMock(CustomEvent::class, ['first', 'second', 'third']);
        $listener
            ->expects(self::never())
            ->method('first')
        ;
        $listener
            ->expects(self::never())
            ->method('second')
        ;
        $listener
            ->expects(self::never())
            ->method('third')
        ;

        ($listenerProvider1 = $this->createMock(ListenerProviderInterface::class))
            ->method('getListenersForEvent')
            ->with($event)
            ->willReturn([[$listener, 'first'], [$listener, 'second']])
        ;
        ($listenerProvider2 = $this->createMock(ListenerProviderInterface::class))
            ->method('getListenersForEvent')
            ->with($event)
            ->willReturn([[$listener, 'third']])
        ;

        $listenerProvider = new ListenerChainableProvider($listenerProvider1);
        $listenerProvider->appendListenerProvider($listenerProvider2);
        $listenerProvider->appendListenerProvider($listenerProvider2);

        $expected = [
            [$listener, 'first'],
            [$listener, 'second'],
            [$listener, 'third'],
        ];
        self::assertSame($expected, $listenerProvider->getListenersForEvent($event));
    }
}
