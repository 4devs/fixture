<?php

namespace FDevs\Fixture\DataGenerator\Combinator;

interface CombinableInterface
{
    /**
     * Generator type.
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Options passed to generator.
     *
     * @return array
     */
    public function getOptions(): array;

    /**
     * @return null|OptionsModifierInterface
     */
    public function getOptionsModifier(): ?OptionsModifierInterface;
}
