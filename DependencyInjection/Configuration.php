<?php

namespace FDevs\Fixture\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    private const VALUE_ADAPTER_DOCTRINE_FIXTURE_FACTORY = 'f_devs_fixture.adapter.doctrine.fixture_factory';
    private const VALUE_ADAPTER_DOCTRINE_REFERENCE_REPOSITORY_FACTORY = 'f_devs_fixture.adapter.doctrine.reference_repository_factory';
    private const VALUE_ADAPTER_DOCTRINE_CONTAINER = 'service_container';

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('f_devs_fixture');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->append($this->createLoadCommandNode())
            ->append($this->createAdapterDoctrine())
        ;

        return $treeBuilder;
    }

    /**
     * @return NodeDefinition
     */
    private function createLoadCommandNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('load_command');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('context_handler')
                    ->info('Service id of ContextHandlerInterface. ' .
                        'If set, will be created LoadContextSubscriber injecting that service, to set ' .
                        'LoadCommand context on execute. Otherwise context would be empty')
                ->end()
            ->end()
        ;

        return $rootNode;
    }

    /**
     * @return NodeDefinition
     */
    private function createAdapterDoctrine(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('adapter_doctrine');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('fixtures')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('path')
                                ->isRequired()
                                ->info('Path to doctrine fixtures directory or file')
                                ->validate()
                                    ->ifTrue(function ($path) {
                                        return !\is_dir($path) && !\is_file($path);
                                    })
                                    ->thenInvalid('Path must be path to directory or file')
                                ->end()
                            ->end()
                            ->scalarNode('manager')
                                ->isRequired()
                                ->info('Service id of doctrine object manager for fixtures. ' .
                                    'MUST implement Doctrine\Common\Persistence\ObjectManager')
                            ->end()
                            ->scalarNode('factory')
                                ->isRequired()
                                ->info('MUST implement FDevs\Fixture\Adapter\Doctrine\FixtureFactoryInterface')
                            ->end()
                            ->scalarNode('container')
                                ->info('MUST implement Symfony\Component\DependencyInjection\ContainerInterface')
                            ->end()
                            ->scalarNode('reference_repository_factory')
                                ->info('MUST implement FDevs\Fixture\Adapter\Doctrine\ReferenceRepositoryFactoryInterface')
                            ->end()
                            ->arrayNode('dependencies')
                                ->defaultValue([])
                                ->info('List of fixtures must be loaded before that fixtures')
                                ->scalarPrototype()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('manager')
                    ->info('Service id of doctrine object manager for fixtures. ' .
                        'MUST implement Doctrine\Common\Persistence\ObjectManager' .
                        'Applied to each element in fixtures, if not defiend')
                ->end()
                ->scalarNode('factory')
                    ->info('Service id of fixtures factory. ' .
                        'MUST implement FDevs\Fixture\Adapter\Doctrine\FixtureFactoryInterface. ' .
                        'Applied to each element in fixtures, if not defiend')
                ->end()
                ->scalarNode('container')
                    ->info('Service id of container to set in doctrine fixtures. ' .
                        'MUST implement Symfony\Component\DependencyInjection\ContainerInterface. ' .
                        'Applied to each element in fixtures, if not defiend')
                ->end()
                ->scalarNode('reference_repository_factory')
                    ->info('Service id of factory to create reference repository for doctrine fixtures. ' .
                        'MUST implement FDevs\Fixture\Adapter\Doctrine\ReferenceRepositoryFactoryInterface. ' .
                        'Applied to each element in fixtures, if not defiend')
                ->end()
            ->end()
            ->beforeNormalization() // apply defaults manager and factory for fixtures
                ->always(function ($data) {
                    $defaultManager = $data['manager'] ?? null;
                    $defaultFactory = $data['factory'] ?? self::VALUE_ADAPTER_DOCTRINE_FIXTURE_FACTORY;
                    $defaultContainer = $data['container'] ?? self::VALUE_ADAPTER_DOCTRINE_CONTAINER;
                    $defaultReferenceFactory = $data['reference_repository_factory'] ?? self::VALUE_ADAPTER_DOCTRINE_REFERENCE_REPOSITORY_FACTORY;

                    foreach ($data['fixtures'] as $key => $fixture) {
                        if (null !== $defaultManager && !isset($fixture['manager'])) {
                            $data['fixtures'][$key]['manager'] = $defaultManager;
                        }
                        if (!isset($fixture['factory'])) {
                            $data['fixtures'][$key]['factory'] = $defaultFactory;
                        }
                        if (!isset($fixture['container'])) {
                            $data['fixtures'][$key]['container'] = $defaultContainer;
                        }
                        if (!isset($fixture['reference_repository_factory'])) {
                            $data['fixtures'][$key]['reference_repository_factory'] = $defaultReferenceFactory;
                        }
                    }

                    return $data;
                })
            ->end()
        ;

        return $rootNode;
    }
}
