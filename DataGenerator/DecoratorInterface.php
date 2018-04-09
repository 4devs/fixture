<?php

namespace FDevs\Fixture\DataGenerator;

interface DecoratorInterface
{
    /**
     * @param \Generator $generator
     * @param array      $options
     *
     * @return \Generator
     */
    public function decorate(\Generator $generator, array $options = []): \Generator;
}
