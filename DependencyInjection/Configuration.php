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
                ->arrayNode('backend')
                    ->children()
                        ->enumNode('type')
                            ->values(array('sidekiq', 'immediate'))
                        ->end()
                        ->scalarNode('sidekiq_client_service')->end()
                    ->end()
                ->end()
                ->scalarNode('serializer')->defaultValue('fervo_deferred_event.serializer.base64')->end()
                ->scalarNode('serializer_format')->defaultValue('base64')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
