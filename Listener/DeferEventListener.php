<?php

namespace Fervo\DeferredEventBundle\Listener;

use Symfony\Component\EventDispatcher\Event;
use Fervo\DeferredEventBundle\Event\DeferEvent;
use Fervo\DeferredEventBundle\Event\Queue\EventQueueInterface;

class DeferEventListener
{
    protected $queue;

    public function __construct(EventQueueInterface $queue)
    {
        $this->queue = $queue;
    }

    public function onDeferEvent(DeferEvent $evt)
    {
        $this->queue->deferEvent($evt->getDeferredEvent(), 0);
    }

    public function onNonDeferEvent(Event $evt)
    {
        $this->queue->deferEvent($evt);
    }
}
