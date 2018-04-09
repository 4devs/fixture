<?php

namespace FDevs\Fixture\DataGenerator\Combinator;

interface CombinableFactoryInterface
{
    /**
     * @param string                        $type
     * @param array                         $options
     * @param OptionsModifierInterface|null $modifier
     *
     * @return CombinableGeneratorInterface
     */
    public function create(string $type, array $options = [], OptionsModifierInterface $modifier = null): CombinableInterface;
}
