<?php

namespace FDevs\Fixture\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    private const VALUE_ADAPTER_DOCTRINE_CONTAINER = 'service_container';
    private const VALUE_ADAPTER_DOCTRINE_FIXTURE_FACTORY = 'f_devs_fixture.adapter.doctrine.fixture_factory';
    private const VALUE_ADAPTER_DOCTRINE_REFERENCE_REPOSITORY_FACTORY = 'f_devs_fixture.adapter.doctrine.reference_repository_factory';
    private const VALUE_ADAPTER_DOCTRINE_ORM_PURGER_FACTORY = 'f_devs_fixture.adapter.doctrine.orm_purger_factory';

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('f_devs_fixture');

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
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('load_command');

        $node
            ->children()
                ->arrayNode('purge')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('allowed')
                            ->defaultFalse()
                            ->info('If true, then inject Pur. ' .
                                'If not specified and `allowed` is true, then FDevs\Fixture\CompositePurger injected to subscriber')
                        ->end()
                        ->scalarNode('service_id')
                            ->info('Service id of FDevs\Fixture\PurgerInterface. ' .
                                'If not specified and `allowed` is true, then FDevs\Fixture\CompositePurger injected to subscriber. ' .
                                'Base FDevs\Fixture\CompositePurger injects services tagged "' . FDevsFixtureExtension::TAG_FIXTURE_PURGER . '"')
                        ->end()
                    ->end()
                    ->beforeNormalization()
                        ->ifTrue(function ($value) {
                            return \is_bool($value);
                        })
                        ->then(function ($value) {
                            return [
                                'allowed' => $value,
                            ];
                        })
                    ->end()
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function ($value) {
                            return [
                                'allowed' => true,
                                'service_id' => $value,
                            ];
                        })
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    /**
     * @return NodeDefinition
     */
    private function createAdapterDoctrine(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('adapter_doctrine');
        $node
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
                                    'MUST implement Doctrine\ORM\EntityManagerInterface')
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
                        'MUST implement Doctrine\ORM\EntityManagerInterface' .
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
                ->arrayNode('purge')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('purger_factory')
                            ->defaultValue(self::VALUE_ADAPTER_DOCTRINE_ORM_PURGER_FACTORY)
                            ->info('Service id of factory to create orm purger for doctrine fixtures. ' .
                                'MUST implement FDevs\Fixture\Adapter\Doctrine\ORMPurgerFactoryInterface.')
                        ->end()
                        ->arrayNode('exclude')
                            ->defaultValue([])
                            ->info('List of tables, excluded to purge')
                            ->scalarPrototype()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->scalarNode('orm_purger_factory')
                ->end()
            ->end()
            ->beforeNormalization() // apply defaults manager and factory for fixtures
                ->always(function ($data) {
                    $defaultManager = $data['manager'] ?? null;
                    $defaultFactory = $data['factory'] ?? self::VALUE_ADAPTER_DOCTRINE_FIXTURE_FACTORY;
                    $defaultContainer = $data['container'] ?? self::VALUE_ADAPTER_DOCTRINE_CONTAINER;
                    $defaultReferenceFactory = $data['reference_repository_factory'] ?? self::VALUE_ADAPTER_DOCTRINE_REFERENCE_REPOSITORY_FACTORY;

                    foreach ($data['fixtures'] ?? [] as $key => $fixture) {
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

        return $node;
    }
}
