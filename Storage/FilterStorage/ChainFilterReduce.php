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
     * @param iterable|SupportFilterInterface[]     $filters
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
            $res = $this->handleFilter($filter, $items, $options);
            if (null !== $res) {
                $out = $res;
                if (\iterator_count($out) < 1) {
                    break;
                }
            }
        }

        yield from $out;
    }

    /**
     * @param SupportFilterInterface $filter
     * @param iterable               $items
     * @param array                  $options
     *
     * @return \Iterator|null
     */
    private function handleFilter(SupportFilterInterface $filter, iterable $items, array $options): ?\Iterator
    {
        if (!$filter->support($items, $options)) {
            return null;
        }

        return $filter->filter($items, $options);
    }
}
