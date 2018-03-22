<?php

namespace FDevs\Fixture\DependencyInjection;

use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\Common\DataFixtures\SharedFixtureInterface;
use FDevs\Fixture\Adapter\Doctrine\ORMPurger;
use FDevs\Fixture\Command\ContextHandler\PurgeHandler;
use FDevs\Fixture\Command\EventListeners\Context\PurgeSubscriber;
use FDevs\Fixture\CompositePurger;
use FDevs\Fixture\DependencyInjection\Compiler\LoadCommandContextPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class FDevsFixtureExtension extends Extension
{
    public const TAG_FIXTURE_PURGER = 'fdevs.fixture.purger';

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('command.xml');
        $loader->load('service.xml');
        $loader->load('adapter/doctrine.xml');

        $this
            ->prepareLoadCommandContextSubscriber($config, $container)
            ->prepareAdapterDoctrine($config, $container)
        ;
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     *
     * @return FDevsFixtureExtension
     */
    private function prepareLoadCommandContextSubscriber(array $config, ContainerBuilder $container): self
    {
        if (!isset($config['load_command'])) {
            return $this;
        }
        $cmdConfig = $config['load_command'];

        $loadCommandPrefix = $this->getAlias().'.load_command.';
        $contextHandlerPrefix = $loadCommandPrefix.'context_handler.';
        $eventListenerPrefix = $loadCommandPrefix.'event_listener.context.';

        $this
            ->prepareCommandPurgeOption($cmdConfig['purge'], $container, $contextHandlerPrefix, $eventListenerPrefix)
        ;

        return $this;
    }

    /**
     * @param array            $purgeConfig
     * @param ContainerBuilder $container
     * @param string           $handlerPrefix
     * @param string           $eventPrefix
     *
     * @return FDevsFixtureExtension
     */
    private function prepareCommandPurgeOption(
        array $purgeConfig,
        ContainerBuilder $container,
        string $handlerPrefix,
        string $eventPrefix
    ): self {
        if ($purgeConfig['allowed']) {
            $purgeHandlerDef = new Definition(PurgeHandler::class);
            $purgeHandlerDef
                ->addTag(LoadCommandContextPass::TAG_HANDLER)
            ;
            $container->setDefinition($handlerPrefix.'purge', $purgeHandlerDef);

            $purgerService = isset($purgeConfig['service_id'])
                ? new Reference($purgeConfig['service_id'])
                : $this->createCompositePurgerDefinition(
                    new TaggedIteratorArgument(self::TAG_FIXTURE_PURGER)
                )
            ;
            $purgerSubscriberDef = $this->createPurgeSubscriberDefinition($purgerService);
            $container->setDefinition($eventPrefix.'purge', $purgerSubscriberDef);
        }

        return $this;
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     *
     * @return FDevsFixtureExtension
     */
    private function prepareAdapterDoctrine(array $config, ContainerBuilder $container): self
    {
        if (!isset($config['adapter_doctrine'])) {
            return $this;
        }
        $adapterDoctrinePrefix = $this->getAlias().'.adapter.doctrine.';
        $eventPrefix = $adapterDoctrinePrefix.'event_listener.';

        $adapterConfig = $config['adapter_doctrine'];
        $fixturesConfig = $adapterConfig['fixtures'];

        /** @var Reference[] $managerIds [`id` => Reference]*/
        $managerRefs = [];
        /** @var Definition[] $referenceRepoRefs [`referenceDefKey` => Definition]*/
        $referenceRepoRefs = [];
        foreach ($fixturesConfig as $fixtureConfig) {
            $loader = new Loader();
            $path = $fixtureConfig['path'];
            if (\is_dir($path)) {
                $loader->loadFromDirectory($path);
            } else {
                $loader->loadFromFile($path);
            }

            $dependencies = $fixtureConfig['dependencies'];
            $loadedFixtures = $loader->getFixtures();
            foreach ($loadedFixtures as $loadedFixture) {
                $fixtureClass = \get_class($loadedFixture);
                $fixtureId = $this->getAlias().'adapter.doctrine.fixture.'.$fixtureClass;
                $managerId = $fixtureConfig['manager'];
                if (!isset($managerRefs[$managerId])) {
                    $managerRefs[$managerId] = new Reference($managerId);
                }
                $managerRef = $managerRefs[$managerId];
                $doctrineDef = new Definition($fixtureClass);

                if (
                    isset($fixtureConfig['container'])
                    && \is_a($fixtureClass, ContainerAwareInterface::class, true)
                ) {
                    $doctrineDef->addMethodCall('setContainer', [new Reference($fixtureConfig['container'])]);
                }

                if (
                    isset($fixtureConfig['reference_repository_factory'])
                    && \is_a($fixtureClass, SharedFixtureInterface::class, true)
                ) {
                    $referenceRefKey = $fixtureConfig['reference_repository_factory'].':'.$managerId;
                    if (!isset($referenceRepoRefs[$referenceRefKey])) {
                        $def = new Definition(ReferenceRepository::class);
                        $def
                            ->setFactory([new Reference($fixtureConfig['reference_repository_factory']), 'create'])
                            ->addArgument($managerRef)
                        ;
                        $repoServiceId = $adapterDoctrinePrefix . 'reference_repository_' . \count($referenceRepoRefs);
                        $container->setDefinition($repoServiceId, $def);
                        $referenceRepoRefs[$referenceRefKey] = new Reference($repoServiceId);
                    }
                    $doctrineDef->addMethodCall('setReferenceRepository', [$referenceRepoRefs[$referenceRefKey]]);
                }

                $def = new Definition($fixtureClass);
                $def
                    ->setFactory([new Reference($fixtureConfig['factory']), 'create'])
                    ->addArgument($doctrineDef)
                    ->addArgument($managerRef)
                    ->addArgument($dependencies)
                    ->addTag('f_devs_fixture.fixture', [
                        'id' => $fixtureClass,
                    ])
                ;

                $container->setDefinition($fixtureId, $def);
                $dependencies = [$fixtureClass];
            }
        }

        if (!empty($managerRefs) && isset($adapterConfig['purge'])) {
            $purgeConfig = $adapterConfig['purge'];

            $emPurgerDefs = \array_map(function (Reference $managerRef) use ($purgeConfig) {
                $def = new Definition(ORMPurger::class);
                $def
                    ->setFactory([new Reference($purgeConfig['purger_factory']), 'create'])
                    ->addArgument($managerRef)
                ;

                return $def;
            }, $managerRefs);

            $compositePurgerDef = $this->createCompositePurgerDefinition($emPurgerDefs);
            $purgeSubscriber = $this->createPurgeSubscriberDefinition($compositePurgerDef);
            $container->setDefinition($eventPrefix.'purge', $purgeSubscriber);
        }

        return $this;
    }

    /**
     * Arguments injected into purger
     *
     * @return Definition
     */
    private function createCompositePurgerDefinition(): Definition
    {
        return new Definition(CompositePurger::class, \func_get_args());
    }

    /**
     * Arguments injected into subscriber
     *
     * @return Definition
     */
    private function createPurgeSubscriberDefinition(): Definition
    {
        $def = new Definition(PurgeSubscriber::class, \func_get_args());
        $def
            ->addTag('kernel.event_subscriber')
        ;

        return $def;
    }
}
