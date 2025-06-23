<?php

namespace Oro\Bundle\HealthCheckBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('oro_health_check');

        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('maintenance_driver')
                ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('options')
                        ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('file_path')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('last_cron_execution_cache')
                ->addDefaultsIfNotSet()
                    ->info('Describes the configuration options for cron last execution cache command.')
                    ->children()
                        ->integerNode('ttl')
                        ->info('Set the TTL for the last cron command execution to check the health of cron commands.')
                        ->defaultValue(900) // 15 min
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
