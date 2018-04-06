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
        $filters = \array_filter($this->filters, function (ChainedFilterInterface $filter) use ($options) {
            return $filter->support($options);
        });

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
