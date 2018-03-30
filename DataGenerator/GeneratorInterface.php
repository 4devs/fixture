<?php

namespace FDevs\Fixture\DataGenerator;

interface GeneratorInterface
{
    /**
     * @param array $options
     *
     * @return \Iterator Iterator of array
     */
    public function generate(array $options = []): \Iterator;
}
