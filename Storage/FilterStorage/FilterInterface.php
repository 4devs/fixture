<?php

namespace FDevs\Fixture\Storage\FilterStorage;

interface FilterInterface
{
    /**
     * @param \Generator $items
     * @param array    $options ['name' => value]
     *
     * @return \Generator Generator of filtered items. Keys of items must be retained
     */
    public function filter(\Generator $items, array $options): \Generator;
}
