<?php

namespace FDevs\Fixture\DataGenerator;

use FDevs\Container\ServiceLocator;

class GeneratorManager extends ServiceLocator implements GeneratorManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate(string $type, array $options = []): \Generator
    {
        $generator = $this->getGenerator($type);

        return $generator->generate($options);
    }

    /**
     * @param string $type
     *
     * @return GeneratorInterface
     */
    private function getGenerator(string $type): GeneratorInterface
    {
        return $this->get($type);
    }
}
