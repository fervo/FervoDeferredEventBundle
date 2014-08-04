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
            ->beforeNormalization()
                ->ifTrue(function ($v) { return is_array($v) && !array_key_exists('backends', $v); })
                ->then(function ($v) {
                    $backend = array();

                    if (!isset($v['backend'])) {
                        //if not 'backend' or 'backends' are configured
                        $backend = array('type' => 'immediate');
                    } else {
                        foreach ($v['backend'] as $key => $value) {
                            $backend[$key] = $v['backend'][$key];
                            unset($v['backend'][$key]);
                        }
                    }

                    $v['default_backend'] = isset($v['default_backend']) ? (string) $v['default_backend'] : 'default';
                    $v['backends'] = array($v['default_backend'] => $backend);

                    unset($v['backend']);

                    return $v;
                })

            ->end()
            ->validate()
                ->ifTrue(function($v) { return !array_key_exists($v['default_backend'], $v['backends']); })
                ->thenInvalid('The configuration "default_backend" must be referring to the name of one of your backends.')
            ->end()
            ->children()
                ->scalarNode('default_backend')->cannotBeEmpty()->defaultValue('default')->end()
                ->append($this->getBackendsNode())
                ->scalarNode('serializer')->defaultValue('fervo_deferred_event.serializer.base64')->end()
                ->scalarNode('serializer_format')->defaultValue('base64')->end()
            ->end()
        ;

        return $treeBuilder;
    }


    private function getBackendsNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('backends');

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
                ->prototype('array')
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
                        ->scalarNode('vhost')->defaultValue('/')->end()
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
        ;

        return $node;
    }
}
