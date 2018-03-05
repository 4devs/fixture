<?php

namespace FDevs\Fixture\Storage\FilterStorage;

interface SupportFilterInterface extends FilterInterface
{
    /**
     * @param array $items
     * @param array $options
     *
     * @return bool
     */
    public function support(array $items, array $options): bool;
}
