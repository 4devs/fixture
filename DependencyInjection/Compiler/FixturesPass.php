<?php

namespace FDevs\Fixture\DependencyInjection\Compiler;

use FDevs\Executor\DependentExecutableIterator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class FixturesPass implements CompilerPassInterface
{
    private const TAG_NAME = 'f_devs_fixture.fixture';

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(DependentExecutableIterator::class)) {
            return;
        }

        $executor = $container->getDefinition(DependentExecutableIterator::class);

        $fixtures = [];
        $fixtureService = $container->findTaggedServiceIds(self::TAG_NAME);
        foreach ($fixtureService as $id => $tags) {
            $def = $container->getDefinition($id);
            if ($def->isAbstract()) {
                continue;
            }

            $fixtures[$id] = new Reference($id);
        }

        $executor->replaceArgument(0, $fixtures);
    }
}
