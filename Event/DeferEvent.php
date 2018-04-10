<?php

namespace Fervo\DeferredEventBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class DeferEvent extends Event
{
    protected $deferredEvent;

    public function __construct($deferredName, Event $deferredEvent = null)
    {
        if (!$deferredEvent) {
            $deferredEvent = new Event();
        }

        $this->deferredEvent = $deferredEvent;
    }

    public function getDeferredEvent()
    {
        return $this->deferredEvent;
    }
}
