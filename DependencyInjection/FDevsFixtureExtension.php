<?php

namespace FDevs\Fixture\DependencyInjection;

use FDevs\Fixture\Command\EventListeners\LoadContextSubscriber;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
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
        $loader->load('executor.xml');

        $this
            ->prepareLoadCommandContextSubsciber($config, $container)
        ;
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     *
     * @return FDevsFixtureExtension
     */
    private function prepareLoadCommandContextSubsciber(array $config, ContainerBuilder $container): self
    {
        $configHandler = $config['load_command']['context_handler'] ?? null;

        if (
            null !== $configHandler
            && $container->hasDefinition($configHandler)
        ) {
            $handlerDef = $container->getDefinition($configHandler);

            $def = new Definition(LoadContextSubscriber::class);
            $def
                ->replaceArgument('$contextHandler', $handlerDef)
                ->addTag('kernel.event_subscriber')
            ;

            $container->setDefinition(LoadContextSubscriber::class, $def);
        }

        return $this;
    }
}
