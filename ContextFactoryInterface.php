<?php

namespace FDevs\Fixture;

use FDevs\Executor\ContextInterface;

interface ContextFactoryInterface
{
    /**
     * @param iterable $context
     *
     * @return ContextInterface
     */
    public function createContext(iterable $context = []): ContextInterface;
}
