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
                        ->scalarNode('serializer')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
