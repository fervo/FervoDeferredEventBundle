<?php

namespace Fervo\DeferredEventBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fervo_dispatch');

        $rootNode
            ->children()
                ->arrayNode('backend')->addDefaultsIfNotSet()
                    ->children()
                        ->enumNode('type')
                            ->values(array('sidekiq', 'immediate', 'amqp'))
                            ->defaultValue('immediate')
                        ->end()
                        ->scalarNode('sidekiq_client_service')->end()
                        ->arrayNode('amqp_config')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('host')->defaultValue('localhost')->end()
                                ->integerNode('port')->defaultValue(5672)->end()
                                ->scalarNode('user')->defaultValue('guest')->end()
                                ->scalarNode('pass')->defaultValue('guest')->end()
                            ->end()
                        ->end()
                        ->arrayNode('message_headers')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('php_bin')->defaultNull()->end()
                                ->scalarNode('console_path')->defaultNull()->end()
                                ->scalarNode('dispatch_path')->defaultNull()->end()
                                ->scalarNode('fastcgi_host')->cannotBeEmpty()->defaultValue('localhost')->end()
                                ->integerNode('fastcgi_port')->cannotBeEmpty()->defaultValue(9000)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('serializer')->defaultValue('fervo_deferred_event.serializer.base64')->end()
                ->scalarNode('serializer_format')->defaultValue('base64')->end()
                ->booleanNode('debug')->defaultFalse()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
