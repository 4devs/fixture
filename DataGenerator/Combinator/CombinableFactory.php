<?php

namespace FDevs\Fixture\DataGenerator\Combinator;

class CombinableFactory implements CombinableFactoryInterface
{
    /**
     * @var string
     */
    private $combinableClass;

    /**
     * CombinableGeneratorFactory constructor.
     *
     * @param string $combinableClass Must be instance of FDevs\Fixture\DataGenerator\Combinator\Combinable
     */
    public function __construct(string $combinableClass = Combinable::class)
    {
        $this->combinableClass = $combinableClass;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $type, array $options = [], OptionsModifierInterface $modifier = null): CombinableInterface
    {
        /** @var Combinable $combinable */
        $combinable = new $this->combinableClass();
        $combinable
            ->setType($type)
            ->setOptions($options)
            ->setOptionsModifier($modifier)
        ;

        return $combinable;
    }
}
