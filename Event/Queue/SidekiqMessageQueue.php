<?php

namespace Fervo\DeferredEventBundle\Event\Queue;

use Fervo\DeferredEventBundle\Model\QueueMessage;
use SidekiqJobPusher\Client;
use Symfony\Component\Serializer\SerializerInterface;

class SidekiqMessageQueue implements MessageQueueInterface
{
    /**
     * @var \SidekiqJobPusher\Client sidekiq
     */
    protected $sidekiq;

    /**
     * @var \Symfony\Component\Serializer\SerializerInterface eventSerializer
     *
     */
    protected $eventSerializer;

    /**
     * @var  serializerFormat
     *
     */
    protected $serializerFormat;

    /**
     * @param Client $sidekiq
     * @param SerializerInterface $eventSerializer
     * @param $serializerFormat
     */
    public function __construct(Client $sidekiq, SerializerInterface $eventSerializer, $serializerFormat)
    {
        $this->sidekiq = $sidekiq;
        $this->serializerFormat = $serializerFormat;
        $this->eventSerializer = $eventSerializer;
    }

    /**
     *
     *
     * @param QueueMessage $message
     * @param int $delay
     *
     */
    public function addMessage(QueueMessage $message, $delay = 0)
    {
        //The sidekiq worker does not support message headers
        $eventData = $this->eventSerializer->serialize($message->getData(), $this->serializerFormat);

        $this->sidekiq->perform('DeferEvent', [$eventData]);
    }
}
