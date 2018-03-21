<?php

namespace FDevs\Fixture\DependencyInjection\Compiler;

use FDevs\Fixture\Command\CompositeContextHandler;
use FDevs\Fixture\Command\EventListeners\LoadContextSubscriber;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class LoadCommandContextPass implements CompilerPassInterface
{
    public const TAG_HANDLER = 'f_devs_fixture.load_command.context_handler';

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds(self::TAG_HANDLER);
        if (empty($taggedServices)) {
            return;
        }

        $handlerDefs = \array_map(function (string $serviceId) use ($container) {
            return $container->getDefinition($serviceId);
        }, \array_keys($taggedServices));


        $compositeHandlerDef = new Definition(CompositeContextHandler::class, [$handlerDefs]);

        $subscriberDef = new Definition(LoadContextSubscriber::class);
        $subscriberDef
            ->addArgument($compositeHandlerDef)
            ->addTag('kernel.event_subscriber')
        ;

        $container->setDefinition('f_devs_fixture.load_command.event_listener.load_context', $subscriberDef);
    }
}
