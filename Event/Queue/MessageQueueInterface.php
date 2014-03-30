<?php

namespace Fervo\DeferredEventBundle\Event\Queue;

use Fervo\DeferredEventBundle\Model\QueueMessage;

interface MessageQueueInterface
{
    public function addMessage(QueueMessage $message, $delay = 0);
}
