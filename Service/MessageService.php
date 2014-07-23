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
class MessageService implements MessageHeaderAwareInterface
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
    protected $headers;

    /**
     * @param array $headers
     * @param SerializerInterface $eventSerializer
     * @param $serializerFormat
     */
    public function __construct(array $headers, SerializerInterface $eventSerializer, $serializerFormat)
    {
        $this->headers=$headers;
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

        foreach ($this->headers as $key=>$value) {
            $message->addHeader($key, $value);
        }

        return $message;
    }

    /**
     * @param string $name
     * @param mixed $value
     *
     * @return $this
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return null
     */
    public function getHeader($name)
    {
        if (isset($this->headers[$name])) {
            return $this->headers[$name];
        }

        return null;
    }
}