<?php

namespace FDevs\Fixture\Storage\FilterStorage;

class ChainFilterReduce implements FilterInterface
{
    /**
     * @var iterable|SupportFilterInterface[]
     */
    private $filters;

    /**
     * ChainFilter constructor.
     *
     * @param iterable                     $filters
     */
    public function __construct(iterable $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(iterable $items, array $options): \Iterator
    {
        $out = $items;
        foreach ($this->filters as $filter) {
            if ($filter->support($out, $options)) {
                $out = $filter->filter($out, $options);
                if (\iterator_count($out) < 1) {
                    break;
                }
            }
        }

        yield from $out;
    }
}
