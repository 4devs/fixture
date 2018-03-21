<?php

namespace FDevs\Fixture\DependencyInjection;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\Common\DataFixtures\SharedFixtureInterface;
use FDevs\Fixture\Command\EventListeners\LoadContextSubscriber;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class FDevsFixtureExtension extends Extension
{
    /**
     * @inheritDoc
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
        $configHandler = $config['load_command']['context_handler'] ?? null;

        if (null !== $configHandler) {
            $handlerRef = new Reference($configHandler);
            $def = new Definition(LoadContextSubscriber::class, [$handlerRef]);
            $def
                ->addTag('kernel.event_subscriber')
            ;

            $container->setDefinition(LoadContextSubscriber::class, $def);
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


        $adapterConfig = $config['adapter_doctrine'];
        $fixturesConfig = $adapterConfig['fixtures'];

        $referenceRepositoryDefs = [];
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
                $fixtureId = $this->getServiceAdapterDoctrinePrefix() . $fixtureClass;
                $managerId = $fixtureConfig['manager'];
                $managerRef = new Reference($managerId);
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
                    if (!isset($referenceRepositoryDefs[$managerId])) {
                        $def = new Definition(ReferenceRepository::class);
                        $def
                            ->setFactory([new Reference($fixtureConfig['reference_repository_factory']), 'create'])
                            ->addArgument($managerRef)
                        ;
                        $referenceRepositoryDefs[$managerId] = $def;
                    }
                    $doctrineDef->addMethodCall('setReferenceRepository', [$referenceRepositoryDefs[$managerId]]);
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

        return $this;
    }

    /**
     * @return string
     */
    private function getServiceAdapterDoctrinePrefix(): string
    {
        return $this->getAlias() . 'adapter.doctrine.fixture.';
    }
}
