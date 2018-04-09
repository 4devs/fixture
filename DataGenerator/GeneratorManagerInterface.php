<?php

namespace FDevs\Fixture\DataGenerator;

interface GeneratorManagerInterface
{
    /**
     * @param string $type
     * @param array  $options
     *
     * @return \Generator
     */
    public function generate(string $type, array $options = []): \Generator;
}
