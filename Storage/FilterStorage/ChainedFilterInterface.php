<?php

namespace FDevs\Fixture\Storage\FilterStorage;

interface ChainedFilterInterface
{
    /**
     * @param array $options
     *
     * @return bool
     */
    public function support(array $options): bool;

    /**
     * @param       $item
     * @param array $options
     *
     * @return bool
     */
    public function isExcluded($item, array $options): bool;
}
