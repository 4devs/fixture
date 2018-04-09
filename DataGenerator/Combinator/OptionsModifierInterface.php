<?php

namespace FDevs\Fixture\DataGenerator\Combinator;

interface OptionsModifierInterface
{
    /**
     * @return string[]
     */
    public function getDependencies(): array;

    /**
     * Modify initial options according to combined data from other generators.
     *
     * @param array $options
     * @param array $combinedData [`generatorKey` => generatorValue]
     *
     * @return array
     */
    public function modifyOptions(array $options, array $combinedData): array;
}
