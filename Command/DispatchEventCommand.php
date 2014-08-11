<?php

namespace Fervo\DeferredEventBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class DispatchEventCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('fervo:deferred-event:dispatch')
            ->setDescription('Dispatch a deferred event')
            ->addArgument('event_data', InputArgument::REQUIRED, 'A serialized event to dispatch')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $serializerFormat = $container->getParameter('fervo_deferred_event.serializer_format');

        $event = $container
            ->get('fervo_deferred_event.serializer')
            ->deserialize($input->getArgument('event_data'), null, $serializerFormat);

        if ($container->getParameter('fervo_deferred_event.debug')) {
            if ($container->has('monolog.logger.deferred_event')) {
                $logger = $container->get('monolog.logger.deferred_event');
            } else {
                $logger = $container->get('logger');
            }

            $eventData = [];
            if (method_exists($event, '__debugInfo')) {
                $eventData = $event->__debugInfo();
            }

            $logger->debug(sprintf("Dispatching event (%s) to event dispatcher.", get_class($event)), $eventData);
        }

        $container->get('fervo_deferred_event.dispatcher')->dispatch($event->getName(), $event);
    }
}
