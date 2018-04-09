<?php

namespace FDevs\Fixture\DataGenerator\Combinator;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class ExpandModifierFactory implements OptionsModifierFactoryInterface
{
    /**
     * @var PropertyAccessorInterface
     */
    private $propAccessor;

    /**
     * @var string
     */
    private $modifierClass;

    /**
     * ExpandModifierFactory constructor.
     *
     * @param PropertyAccessorInterface $propAccessor
     * @param string                    $modifierClass Must be instance of FDevs\Fixture\DataGenerator\Combinator\ExpandModifier
     */
    public function __construct(PropertyAccessorInterface $propAccessor, string $modifierClass = ExpandModifier::class)
    {
        $this->propAccessor = $propAccessor;
        $this->modifierClass = $modifierClass;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options): OptionsModifierInterface
    {
        /** @var ExpandModifier $modifier */
        $modifier = new $this->modifierClass();
        $modifier
            ->setPropAccessor($this->propAccessor)
            ->setMergedOptions($options)
        ;

        return $modifier;
    }
}
