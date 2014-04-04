<?php

namespace Fervo\DeferredEventBundle\Service;

use Fervo\DeferredEventBundle\Model\QueueMessage;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class MessageService
 *
 * @author Tobias Nyholm
 *
 */
class MessageService
{
    /**
     * @var \Symfony\Component\Serializer\SerializerInterface eventSerializer
     *
     */
    protected $eventSerializer;

    /**
     * @var string serializerFormat
     *
     */
    protected $serializerFormat;

    /**
     * @var array config
     *
     */
    protected $config;

    /**
     * @param array $config
     * @param SerializerInterface $eventSerializer
     * @param $serializerFormat
     */
    public function __construct(array $config, SerializerInterface $eventSerializer, $serializerFormat)
    {
        $this->config=$config;
        $this->eventSerializer = $eventSerializer;
        $this->serializerFormat = $serializerFormat;
    }

    /**
     * Create a message and add some headers
     *
     * @param Event $event
     *
     * @return QueueMessage
     */
    public function createMessage(Event $event)
    {
        //serialize the event
        $eventData = $this->eventSerializer->serialize($event, $this->serializerFormat);
        $message = new QueueMessage($eventData);

        foreach ($this->config as $key=>$value) {
            $message->addHeader($key, $value);
        }

        return $message;
    }
} 