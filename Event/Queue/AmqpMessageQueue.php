<?php

namespace Fervo\DeferredEventBundle\Event\Queue;

use Fervo\DeferredEventBundle\Model\QueueMessage;
use Fervo\DeferredEventBundle\Service\MessageService;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class AmqpEventQueue
 *
 * @author Tobias Nyholm
 *
 * This class may communicate with any Advanced Message Queuing Protocol, Like RabbitMQ
 *
 */
class AmqpMessageQueue implements MessageQueueInterface
{
    /**
     * @var \PhpAmqpLib\Connection\AMQPConnection queueConnection
     *
     */
    protected $queueConnection;

    /**
     * @var \PhpAmqpLib\Channel\AMQPChannel queueChannel
     *
     */
    protected $queueChannel;

    /**
     * @var string queueName
     *
     */
    protected $queueName='sf_deferred_events';

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        if (!class_exists('PhpAmqpLib\Connection\AMQPConnection')) {
            throw new \Exception('You need to add "videlalvaro/php-amqplib" to your composer.json');
        }

        //establish connection
        $this->queueConnection = new AMQPConnection($config['host'], $config['port'], $config['user'], $config['pass']);

        //get a channel
        $this->queueChannel = $this->queueConnection->channel();

        //make sure we got a topic
        $this->queueChannel->queue_declare($this->queueName, false, false, false, false);
    }

    /**
     * Put a message on the queue
     *
     * @param Event $event
     * @param int $delay
     *
     */
    public function addMessage(QueueMessage $message, $delay = 0)
    {
        //create a message and publish it
        $queueMessage = new AMQPMessage($message->getFormattedMessage());
        $this->queueChannel->basic_publish($queueMessage, '', $this->queueName);
    }

    /**
     * Clean up after us
     */
    function __destruct()
    {
        $this->queueChannel->close();
        $this->queueConnection->close();
    }
}