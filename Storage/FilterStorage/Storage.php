<?php

namespace FDevs\Fixture\Storage\FilterStorage;

use FDevs\Fixture\Exception\Storage\NotFoundException;
use FDevs\Fixture\Exception\Storage\StoreItemException;
use FDevs\Fixture\Storage\StorageInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Storage implements StorageInterface
{
    private const RESERVED_KEY_CHARACTERS = ['{', '}', '(', ')', '/', '\\', '@'];

    /**
     * @var array
     */
    private $typeKeys = [];
    /**
     * @var FilterInterface|null
     */
    private $filter;
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
     * @param CacheItemPoolInterface $cachePool
     * @param FilterInterface     $filter
     * @param string                 $keyPrefix
     * @param int|null               $ttl
     */
    public function __construct(
        CacheItemPoolInterface $cachePool,
        FilterInterface $filter = null,
        string $keyPrefix = '',
        int $ttl = null
    ) {
        $this->filter = $filter;
        $this->cachePool = $cachePool;
        $this->keyPrefix = $keyPrefix;
        $this->ttl = $ttl;
    }

    /**
     * {@inheritdoc}
     */
    public function find(string $type, array $options): \Generator
    {
        $typeKeys = $this->getTypeKeys($type);
        $data = $this->getDataGenerator($typeKeys);

        yield from null === $this->filter
            ? $data
            : $this->filter->filter($data, $options)
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
        $item = $this->has($key)
            ? $this->getCachePool()->getItem($key)
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
     * @param iterable $keys
     *
     * @throws NotFoundException
     *
     * @return \Generator
     */
    private function getDataGenerator(iterable $keys): \Generator
    {
        foreach ($keys as $key) {
            yield $key => $this->get($key);
        }
    }
}
