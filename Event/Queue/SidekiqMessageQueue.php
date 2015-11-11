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
     * @param Client $sidekiq
     * @param SerializerInterface $eventSerializer
     * @param $serializerFormat
     */
    public function __construct(Client $sidekiq)
    {
        $this->sidekiq = $sidekiq;
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
        $this->sidekiq->perform('DeferEvent', [$message->getData()]);
    }
}
