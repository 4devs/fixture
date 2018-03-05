<?php

namespace FDevs\Fixture\Storage\FilterStorage;

interface FilterInterface
{
    /**
     * @param iterable $items
     * @param array    $options ['name' => value]
     *
     * @return \Iterator Iterator of filtered items
     */
    public function filter(iterable $items, array $options): \Iterator;
}
