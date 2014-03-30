<?php

namespace Fervo\DeferredEventBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class FervoDeferredEventExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setAlias('fervo_deferred_event.serializer', $config['serializer']);

        if (!empty($config['backend']['sidekiq_client_service'])) {
            $def = $container->getDefinition('fervo_deferred_event.queue.sidekiq');
            $def->replaceArgument(0, new Reference($config['backend']['sidekiq_client_service']));
        }

        if ($config['backend']['type']=='amqp') {
            $def = $container->getDefinition('fervo_deferred_event.queue.amqp');
            $def->replaceArgument(0, $config['backend']['amqp_config']);
        }

        //try to set default php bin
        if ($config['backend']['message_headers']['php_bin']==null) {
            if (defined(PHP_BINARY)) {
                //since php 5.4
                $config['backend']['message_headers']['php_bin']=PHP_BINARY;
            } else {
                $config['backend']['message_headers']['php_bin']=PHP_BINDIR.'/php';
            }
        }

        //try to set default app_console
        if ($config['backend']['message_headers']['console_path']==null) {
            $config['backend']['message_headers']['console_path']=$container->getParameter('kernel.root_dir').'/console';
        }

        //try to set default dispatch_path
        if ($config['backend']['message_headers']['dispatch_path']==null) {
            $config['backend']['message_headers']['dispatch_path']=$container->getParameter('kernel.root_dir').'/../bin/dispatch.php';
        }

        //add message headers to the message service
        $def = $container->getDefinition('fervo_deferred_event.service.message_service');
        $def->replaceArgument(0, $config['backend']['message_headers']);

        $container->setParameter('fervo_deferred_event.serializer_format', $config['serializer_format']);

        $container->setAlias(
            'fervo_deferred_event.queue',
            sprintf('fervo_deferred_event.queue.%s', $config['backend']['type'])
        );
    }
}
