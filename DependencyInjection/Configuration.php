<?php

namespace Enqueue\Bundle\DependencyInjection;

use Enqueue\Client\Config;
use Enqueue\Symfony\TransportFactoryInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @var TransportFactoryInterface[]
     */
    private $factories;

    /**
     * @param TransportFactoryInterface[] $factories
     */
    public function __construct(array $factories)
    {
        $this->factories = $factories;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $tb = new TreeBuilder();
        $rootNode = $tb->root('enqueue');

        $transportChildren = $rootNode->children()
            ->arrayNode('transport')->isRequired()->children();

        foreach ($this->factories as $factory) {
            $factory->addConfiguration(
                $transportChildren->arrayNode($factory->getName())
            );
        }

        $rootNode->children()
            ->arrayNode('client')->children()
                ->booleanNode('traceable_producer')->defaultFalse()->end()
                ->scalarNode('prefix')->defaultValue('enqueue')->end()
                ->scalarNode('app_name')->defaultValue('app')->end()
                ->scalarNode('router_topic')->defaultValue('router')->cannotBeEmpty()->end()
                ->scalarNode('router_queue')->defaultValue(Config::DEFAULT_PROCESSOR_QUEUE_NAME)->cannotBeEmpty()->end()
                ->scalarNode('router_processor')->defaultValue('enqueue.client.router_processor')->end()
                ->scalarNode('default_processor_queue')->defaultValue(Config::DEFAULT_PROCESSOR_QUEUE_NAME)->cannotBeEmpty()->end()
                ->integerNode('redelivered_delay_time')->min(0)->defaultValue(0)->end()
            ->end()->end()
            ->booleanNode('job')->defaultFalse()->end()
            ->arrayNode('async_events')
                ->canBeEnabled()
                ->children()
                    ->booleanNode('spool_producer')->defaultFalse()->end()
                ->end()
            ->end()
            ->arrayNode('extensions')->addDefaultsIfNotSet()->children()
                ->booleanNode('doctrine_ping_connection_extension')->defaultFalse()->end()
                ->booleanNode('doctrine_clear_identity_map_extension')->defaultFalse()->end()
                ->booleanNode('signal_extension')->defaultValue(function_exists('pcntl_signal_dispatch'))->end()
            ->end()->end()
        ;

        return $tb;
    }
}
