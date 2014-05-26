<?php

namespace Fervo\DeferredEventBundle\Listener;

use Fervo\DeferredEventBundle\EventDispatcher\DummyEventDispatcher;
use Fervo\DeferredEventBundle\Service\MessageService;
use Symfony\Component\EventDispatcher\Event;
use Fervo\DeferredEventBundle\Event\DeferEvent;
use Fervo\DeferredEventBundle\Event\Queue\MessageQueueInterface;

class DeferEventListener
{
    /**
     * @var \Fervo\DeferredEventBundle\Event\Queue\MessageQueueInterface queue
     */
    protected $queue;

    /**
     * @var \Fervo\DeferredEventBundle\Service\MessageService messageService
     */
    protected $messageService;

    /**
     * @param MessageQueueInterface $queue
     * @param MessageService $messageService
     */
    public function __construct(MessageQueueInterface $queue, MessageService $messageService)
    {
        $this->queue = $queue;
        $this->messageService = $messageService;
    }

    /**
     * When we defer a DeferEvent. (The publisher decides that this should be a defer event)
     *
     * @param DeferEvent $event
     */
    public function onDeferEvent(DeferEvent $event)
    {
        $message=$this->messageService->createMessage($event->getDeferredEvent());
        $this->queue->addMessage($message, 0);
    }

    /**
     * When we defer a normal Event. (The listener decides that this should be a defer event)
     *
     * @param Event $event
     */
    public function onNonDeferEvent(Event $event)
    {
        // save original dispatcher
        $orgDispatcher=$event->getDispatcher();

        // add a dummy dispatcher because we cannot serialize or unserialize PDO instances
        $event->setDispatcher(new DummyEventDispatcher());
        $message=$this->messageService->createMessage($event);
        $this->queue->addMessage($message);

        // reset to the original dispatcher
        $event->setDispatcher($orgDispatcher);
    }
}
