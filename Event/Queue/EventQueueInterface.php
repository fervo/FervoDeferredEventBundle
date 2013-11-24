<?php

namespace Fervo\DeferredEventBundle\Event\Queue;

use Symfony\Component\EventDispatcher\Event;

interface EventQueueInterface
{
    public function deferEvent(Event $event, $delay = 0);
}
