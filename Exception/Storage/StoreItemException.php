<?php

namespace FDevs\Fixture\Exception\Storage;

use Psr\Cache\CacheItemInterface;

class StoreItemException extends StoreException
{
    /**
     * @var CacheItemInterface
     */
    private $item;

    /**
     * StoreItemException constructor.
     *
     * @param CacheItemInterface $item
     */
    public function __construct(CacheItemInterface $item)
    {
        $this->item = $item;

        $msg = 'Failed to store item by key "' . $item->getKey() . '"';
        parent::__construct($msg);
    }

    /**
     * @return CacheItemInterface
     */
    public function getCacheItem(): CacheItemInterface
    {
        return $this->item;
    }
}
