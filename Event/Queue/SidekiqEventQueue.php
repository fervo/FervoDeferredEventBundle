<?php

namespace Fervo\DeferredEventBundle\Event\Queue;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Serializer\SerializerInterface;
use SidekiqJobPusher\Client;

class SidekiqEventQueue implements EventQueueInterface
{
    protected $sidekiq;
    protected $eventSerializer;
    protected $serializerFormat;

    public function __construct(Client $sidekiq, SerializerInterface $eventSerializer, $serializerFormat)
    {
        $this->sidekiq = $sidekiq;
        $this->eventSerializer = $eventSerializer;
        $this->serializerFormat = $serializerFormat;
    }

    public function deferEvent(Event $event, $delay = 0)
    {
        $eventData = $this->eventSerializer->serialize($event, $this->serializerFormat);

        $this->sidekiq->perform('DeferEvent', [$eventData]);
    }
}
