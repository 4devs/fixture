<?php

namespace FDevs\Fixture\Storage\FilterStorage;

use FDevs\Fixture\Exception\Storage\NotFoundException;
use FDevs\Fixture\Exception\Storage\StoreItemException;
use FDevs\Fixture\Storage\StorageInterface;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Storage implements StorageInterface
{
    private const RESERVED_KEY_CHARACTERS = ['{', '}', '(', ')', '/', '\\', '@'];

    /**
     * @var array
     */
    private $typeKeys = [];
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var CacheItemPoolInterface
     */
    private $cachePool;
    /**
     * @var string
     */
    private $keyPrefix;
    /**
     * @var int|null
     */
    private $ttl;

    /**
     * @var array
     */
    private $keyReplaceMapping;

    /**
     * Storage constructor.
     *
     * @param ContainerInterface     $container
     * @param CacheItemPoolInterface $cachePool
     * @param string                 $keyPrefix
     * @param int|null               $ttl
     */
    public function __construct(
        ContainerInterface $container,
        CacheItemPoolInterface $cachePool,
        string $keyPrefix = '',
        int $ttl = null
    ) {
        $this->container = $container;
        $this->cachePool = $cachePool;
        $this->keyPrefix = $keyPrefix;
        $this->ttl = $ttl;
    }

    /**
     * {@inheritdoc}
     */
    public function find(string $type, array $options): \Iterator
    {
        $typeKeys = $this->getTypeKeys($type);
        $items = $this->getCachePool()->getItems($typeKeys);
        $data = $this->getDataIterator($items);
        $filter = $this->findFilter($type);

        yield from null === $filter
            ? $data
            : $filter->filter($data, $options)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function store($data, string $type): string
    {
        $key = $this->createKey($type);

        $pool = $this->getCachePool();
        $item = $pool->getItem($key);
        $item
            ->set($data)
            ->expiresAfter($this->getCacheItemTtl())
        ;

        if (!$pool->save($item)) {
            throw new StoreItemException($item);
        }
        $this->typeKeys[$type][$key] = true;

        return $key;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key)
    {
        $pool = $this->getCachePool();
        $item = $pool->hasItem($key)
            ? $pool->getItem($key)
            : null
        ;

        if (null === $item || !$item->isHit()) {
            $msg = 'Stored data by key "' . $key . '" not found';
            throw new NotFoundException($msg);
        }

        return $item->get();
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return $this->getCachePool()->hasItem($key);
    }

    /**
     * @param string $type
     *
     * @return array
     */
    protected function getTypeKeys(string $type): array
    {
        return isset($this->typeKeys[$type])
            ? \array_keys($this->typeKeys[$type])
            : []
            ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getCachePool(): CacheItemPoolInterface
    {
        return $this->cachePool;
    }

    /**
     * {@inheritdoc}
     */
    protected function getCacheItemTtl(): ?int
    {
        return $this->ttl;
    }

    /**
     * {@inheritdoc}
     */
    protected function createKey(string $type): string
    {
        $key = $this->keyPrefix . $type . '_' . \count($this->getTypeKeys($type));
        if (null === $this->keyReplaceMapping) {
            $this->keyReplaceMapping = \array_fill_keys(self::RESERVED_KEY_CHARACTERS, '_');
        }

        return strtr($key, $this->keyReplaceMapping);
    }

    /**
     * @param string $type
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @return FilterInterface
     */
    private function findFilter(string $type): ?FilterInterface
    {
        return $this->container->has($type)
            ? $this->container->get($type)
            : null
            ;
    }

    /**
     * @param iterable $items
     *
     * @return \Iterator
     */
    private function getDataIterator(iterable $items): \Iterator
    {
        foreach ($items as $key => $item) {
            yield $key => $this->getDataFromCacheItem($item);
        }
    }

    /**
     * @param CacheItemInterface $item
     *
     * @return mixed
     */
    private function getDataFromCacheItem(CacheItemInterface $item)
    {
        return $item->get();
    }
}
