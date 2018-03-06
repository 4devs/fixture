<?php

namespace FDevs\Fixture\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('f_devs_fixture');

        $rootNode
            ->append($this->createLoadCommandNode())
        ;

        return $treeBuilder;
    }

    /**
     * @return NodeDefinition
     */
    private function createLoadCommandNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('load_command');
        $node
            ->arrayPrototype()
                ->children()
                    ->scalarNode('context_handler')
                        ->info('Service id of ContextHandlerInterface. ' .
                            'If set, will be created LoadContextSubscriber injecting that service, to set ' .
                            'LoadCommand context on execute. Otherwise context would be empty')
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
