<?php

namespace FDevs\Fixture\Storage\FilterStorage;

class ChainFilterReduce implements FilterInterface
{
    /**
     * @var iterable|ChainedFilterInterface[]
     */
    private $filters;

    /**
     * ChainFilter constructor.
     *
     * @param iterable|ChainedFilterInterface[] $filters
     */
    public function __construct(iterable $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(\Generator $items, array $options): \Generator
    {
        $filters = [];
        foreach ($this->filters as $filter) {
            if ($filter->support($options)) {
                $filters[] = $filter;
            }
        }

        if (empty($filters)) {
            yield from $items;

            return;
        }

        foreach ($items as $key => $item) {
            foreach ($filters as $filter) {
                if ($filter->isExcluded($item, $options)) {
                    continue 2;
                }
            }

            yield $key => $item;
        }
    }
}
