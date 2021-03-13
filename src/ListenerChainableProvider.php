<?php

namespace Dispify\EventDispatcher;

use Psr\EventDispatcher\ListenerProviderInterface;

class ListenerChainableProvider implements ListenerProviderInterface
{
    /**
     * @var ListenerProviderInterface[]
     */
    private $providers = [];

    public function __construct(ListenerProviderInterface $listenerProvider = null)
    {
        if ($listenerProvider) {
            $this->appendListenerProvider($listenerProvider);
        }
    }

    public function appendListenerProvider(ListenerProviderInterface $listenerProvider)
    {
        $this->providers[\spl_object_hash($listenerProvider)] = $listenerProvider;
    }

    /**
     * @inheritDoc
     */
    public function getListenersForEvent(object $event): iterable
    {
        $listeners = [];
        foreach ($this->providers as $provider) {
            $listeners = array_merge($listeners, $provider->getListenersForEvent($event));
        }

        return $listeners;
    }
}
