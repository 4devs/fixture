<?php

namespace FDevs\Fixture\DataGenerator;

interface GeneratorInterface
{
    /**
     * @param array $options
     *
     * @return \Generator Generator of data
     */
    public function generate(array $options): \Generator;
}
