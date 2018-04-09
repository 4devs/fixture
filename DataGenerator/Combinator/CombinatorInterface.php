<?php

namespace FDevs\Fixture\DataGenerator\Combinator;

interface CombinatorInterface
{
    /**
     * Generate all available combinations.
     *
     * @param CombinableGeneratorInterface[] $generators
     *
     * @return \Generator
     */
    public function combine(array $generators): \Generator;
}
