<?php

namespace FDevs\Fixture;

use FDevs\Executor\ContextInterface;

class ContextFactory implements ContextFactoryInterface
{
    /**
     * @var string
     */
    private $contextClass;

    /**
     * ContextFactory constructor.
     *
     * @param string $contextClass
     */
    public function __construct(string $contextClass)
    {
        $this->contextClass = $contextClass;
    }

    /**
     * @inheritDoc
     */
    public function createContext(iterable $context = []): ContextInterface
    {
        $out = $this->createFromClass();
        foreach ($context as $name => $value) {
            $out->addContext($name, $value);
        }

        return $out;
    }

    /**
     * @return ContextInterface
     */
    private function createFromClass(): ContextInterface
    {
        $className = $this->contextClass;

        return new $className();
    }
}
