<?php

namespace FDevs\Fixture\DataGenerator;

interface DecoratorManagerInterface
{
    /**
     * @param string     $type
     * @param \Generator $generator
     * @param array      $options
     *
     * @return \Generator
     */
    public function decorate(string $type, \Generator $generator, array $options = []): \Generator;
}
