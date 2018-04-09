<?php

namespace FDevs\Fixture\DataGenerator\Combinator;

interface OptionsModifierFactoryInterface
{
    /**
     * @param array $options
     *
     * @return OptionsModifierInterface
     */
    public function create(array $options): OptionsModifierInterface;
}
