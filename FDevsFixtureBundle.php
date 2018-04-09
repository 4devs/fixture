<?php

namespace FDevs\Fixture;

use FDevs\Container\Compiler\ServiceLocatorPass;
use FDevs\Fixture\DependencyInjection\FDevsFixtureExtension;
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
            ->addCompilerPass(new ServiceLocatorPass(
                'f_devs_fixture.dependent_fixture_iterator',
                'f_devs_fixture.fixture'
            ))
            ->addCompilerPass(new ServiceLocatorPass(
                'f_devs_fixture.data_generator.generator_manager',
                'f_devs_fixture.data_generator.generator'
            ))
            ->addCompilerPass(new ServiceLocatorPass(
                'f_devs_fixture.data_generator.decorator_manager',
                'f_devs_fixture.data_generator.decorator'
            ))
        ;
    }
}
