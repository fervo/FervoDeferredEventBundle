<?php


namespace Fervo\DeferredEventBundle\EventDispatcher;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Tobias Nyholm
 *
 * Use this class because we cannot serialize or unserialize PDO instances
 */
class DummyEventDispatcher implements EventDispatcherInterface
{
    public function dispatch($eventName, Event $event = null) { }

    public function addListener($eventName, $listener, $priority = 0) { }

    public function addSubscriber(EventSubscriberInterface $subscriber) { }

    public function removeListener($eventName, $listener) { }

    public function removeSubscriber(EventSubscriberInterface $subscriber) { }

    public function getListeners($eventName = null) { }

    public function hasListeners($eventName = null) { }
}