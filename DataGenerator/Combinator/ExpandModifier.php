<?php

namespace FDevs\Fixture\DataGenerator\Combinator;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class ExpandModifier implements OptionsModifierInterface
{
    /**
     * @var PropertyAccessorInterface
     */
    private $propAccessor;

    /**
     * @var string[]
     */
    private $dependencies;

    /**
     * @var array [`combinedDataKey` => `optionsPath`]
     */
    private $mergedOptions;

    /**
     * @param PropertyAccessorInterface $propAccessor
     *
     * @return ExpandModifier
     */
    public function setPropAccessor(PropertyAccessorInterface $propAccessor): self
    {
        $this->propAccessor = $propAccessor;

        return $this;
    }

    /**
     * @param array $mergedOptions
     *
     * @return ExpandModifier
     */
    public function setMergedOptions(array $mergedOptions): self
    {
        $this->dependencies = \array_keys($mergedOptions);
        $this->mergedOptions = \array_map(function ($pathParts) {
            $path = \is_string($pathParts)
                ? $pathParts
                : \implode('][', (array) $pathParts)
            ;

            return '['.$path.']';
        }, $mergedOptions);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyOptions(array $options, array $combinedData): array
    {
        foreach ($this->mergedOptions as $combinedKey => $optPath) {
            $this->propAccessor->setValue($options, $optPath, $combinedData[$combinedKey]);
        }

        return $options;
    }
}
