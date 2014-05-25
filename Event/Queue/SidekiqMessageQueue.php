<?php

namespace Fervo\DeferredEventBundle\Event\Queue;

use Fervo\DeferredEventBundle\Model\QueueMessage;
use SidekiqJobPusher\Client;

class SidekiqMessageQueue implements MessageQueueInterface
{
    /**
     * @var \SidekiqJobPusher\Client sidekiq
     */
    protected $sidekiq;

    /**
     * @param Client $sidekiq
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
        $eventData = $this->eventSerializer->serialize($message->getData(), $this->serializerFormat);

        $this->sidekiq->perform('DeferEvent', [$eventData]);
    }
}
