<?php

namespace FDevs\Fixture;

use FDevs\Fixture\DependencyInjection\Compiler\FixturesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FDevsFixtureBundle extends Bundle
{
    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new FixturesPass())
        ;
    }
}
