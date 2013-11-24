<?php

namespace Fervo\DeferredEventBundle\Event\Queue;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Serializer\SerializerInterface;
use SidekiqJobPusher\Client;

class SidekiqEventQueue implements EventQueueInterface
{
    protected $sidekiq;
    protected $eventSerializer;

    public function __construct(Client $sidekiq, SerializerInterface $eventSerializer)
    {
        $this->sidekiq = $sidekiq;
        $this->eventSerializer = $eventSerializer;
    }

    public function deferEvent(Event $event, $delay = 0)
    {
        $eventData = $this->eventSerializer->serialize($event, 'base64');

        $this->sidekiq->perform('DeferEvent', [$eventData]);
    }
}
