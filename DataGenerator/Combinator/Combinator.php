<?php

namespace FDevs\Fixture\DataGenerator\Combinator;

use FDevs\Fixture\DataGenerator\GeneratorManagerInterface;

class Combinator implements CombinatorInterface
{
    /**
     * @var GeneratorManagerInterface
     */
    private $generatorManager;

    /**
     * Combinator constructor.
     *
     * @param GeneratorManagerInterface $generatorManager
     */
    public function __construct(GeneratorManagerInterface $generatorManager)
    {
        $this->generatorManager = $generatorManager;
    }

    /**
     * {@inheritdoc}
     */
    public function combine(array $generators): \Generator
    {
        $keys = \array_keys($generators);

        return $this->proceed($generators, $keys);
    }

    /**
     * @param CombinableGeneratorInterface[] $generators
     * @param string[]                       $keys
     * @param array                          $combinedData
     * @param int                            $idx
     *
     * @return \Generator
     */
    private function proceed(array $generators, array &$keys, array $combinedData = [], int &$idx = 0): \Generator
    {
        $key = \array_pop($keys);
        if (null === $key) {
            yield $idx++ => $combinedData;

            return;
        }

        $comGenerator = $generators[$key];
        $optionsModifier = $comGenerator->getOptionsModifier();
        $options = $comGenerator->getOptions();
        if (null !== $optionsModifier) {
            foreach ($optionsModifier->getDependencies() as $dependencyKey) {
                if (!isset($combinedData[$dependencyKey])) {
                    \array_unshift($keys, $key);
                    yield from $this->proceed($generators, $keys, $combinedData, $idx);

                    return;
                }
            }
            $options = $optionsModifier->modifyOptions($options, $combinedData);
        }

        $generator = $this->generatorManager->generate($comGenerator->getType(), $options);
        foreach ($generator as $item) {
            $combinedData[$key] = $item;
            yield from $this->proceed($generators, $keys, $combinedData, $idx);
        }

        $keys[] = $key;
    }
}
