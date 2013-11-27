<?php

namespace Fervo\DeferredEventBundle\Event\Queue;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;

class ImmediateEventQueue implements EventQueueInterface
{
    protected $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function deferEvent(Event $event, $delay = 0)
    {
        $this->eventDispatcher->dispatch($event->getName(), $event);
    }
}
