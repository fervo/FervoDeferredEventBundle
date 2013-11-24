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
        $event = $this->getContainer()
            ->get('fervo_deferred_event.serializer')
            ->deserialize($input->getArgument('event_data'), null, null);

        $this->getContainer()->get('event_dispatcher')->dispatch($event->getName(), $event);
    }
}
