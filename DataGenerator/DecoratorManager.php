<?php

namespace FDevs\Fixture\DataGenerator;

use FDevs\Container\ServiceLocator;

class DecoratorManager extends ServiceLocator implements DecoratorManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function decorate(string $type, \Generator $generator, array $options = []): \Generator
    {
        $decorator = $this->getDecorator($type);

        return $decorator->decorate($generator, $options);
    }

    /**
     * @param string $type
     *
     * @return DecoratorInterface
     */
    private function getDecorator(string $type): DecoratorInterface
    {
        return $this->get($type);
    }
}
