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
class FervoDispatchExtension extends Extension
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

        if ($config['backend']['sidekiq_client_service']) {
            $def = $container->getDefinition('fervo_dispatch.queue.sidekiq');
            $def->replaceArgument(0, new Reference($config['backend']['sidekiq_client_service']));
        }

        $container->setAlias(
            'fervo_dispatch.queue',
            sprintf('fervo_dispatch.queue.%s', $config['backend']['type'])
        );
    }
}
