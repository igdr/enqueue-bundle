<?php

declare(strict_types=1);

namespace Enqueue\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

class ClientFactory
{
    public static function getConfiguration(bool $debug, string $name = 'client'): NodeDefinition
    {
        $builder = new ArrayNodeDefinition($name);

        $builder->children()
            ->booleanNode('traceable_producer')->defaultValue($debug)->end()
            ->scalarNode('prefix')->defaultValue('enqueue')->end()
            ->scalarNode('separator')->defaultValue('.')->end()
            ->scalarNode('app_name')->defaultValue('app')->end()
            ->scalarNode('router_topic')->defaultValue('default')->cannotBeEmpty()->end()
            ->scalarNode('router_queue')->defaultValue('default')->cannotBeEmpty()->end()
            ->scalarNode('router_processor')->defaultNull()->end()
            ->integerNode('redelivered_delay_time')->min(0)->defaultValue(0)->end()
            ->scalarNode('default_queue')->defaultValue('default')->cannotBeEmpty()->end()
            ->arrayNode('driver_options')
            ->addDefaultsIfNotSet()
            ->info('The array contains driver specific options')
            ->ignoreExtraKeys(false)
            ->end()
            ->end();

        return $builder;
    }
}
