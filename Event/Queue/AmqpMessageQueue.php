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
     * @var boolean batchPublishing
     */
    private $batchPublishing;

    /**
     * Make sure to initialize before we post our first message.
     * @var bool initialized
     */
    private $initialized = false;

    /**
     * @var array config
     */
    private $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        if (!class_exists('PhpAmqpLib\Connection\AMQPConnection')) {
            throw new \Exception('You need to add "videlalvaro/php-amqplib" to your composer.json');
        }

        //save config
        $this->config = $config;
    }

    /**
     * Clean up after us
     */
    function __destruct()
    {
        if (!$this->initialized) {
            return;
        }

        if ($this->batchPublishing) {
            //publish all our messages at once
            $this->queueChannel->publish_batch();
        }

        $this->queueChannel->close();
        $this->queueConnection->close();
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
        $this->init();

        //create a message and publish it
        $queueMessage = new AMQPMessage($message->getFormattedMessage());

        if ($this->batchPublishing) {
            //add to batch queue
            $this->queueChannel->batch_basic_publish($queueMessage, '', $this->config['queue_name']);
        } else {
            //publish now
            $this->queueChannel->basic_publish($queueMessage, '', $this->config['queue_name']);
        }
    }



    /**
     * Create queue objects and init the class
     * @param array $config
     */
    private function init()
    {
        if ($this->initialized) {
            return;
        }

        $this->initialized = true;

        $this->batchPublishing = $this->config['batch_publishing'];

        //establish connection
        $this->queueConnection = new AMQPConnection($this->config['host'], $this->config['port'], $this->config['user'], $this->config['pass']);

        //get a channel
        $this->queueChannel = $this->queueConnection->channel();

        //make sure we got a topic
        $this->queueChannel->queue_declare($this->config['queue_name'], false, $this->config['durable'], false, false);
    }
}
