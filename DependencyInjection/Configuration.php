<?php

namespace Oro\Bundle\HealthCheckBundle\DependencyInjection;

use Oro\Bundle\HealthCheckBundle\Drivers\FileDriver;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration for oro_health_check node
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('oro_health_check');

        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('maintenance_driver')
                ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')
                            ->defaultValue(FileDriver::class)
                        ->end()
                        ->arrayNode('options')
                        ->addDefaultsIfNotSet()
                            ->children()
                                ->integerNode('ttl')
                                    ->defaultValue(600)
                                ->end()
                                ->scalarNode('file_path')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
