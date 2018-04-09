<?php

namespace FDevs\Fixture\Adapter\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use FDevs\Fixture\Storage\FilterStorage\FilterInterface;
use FDevs\Fixture\Storage\FilterStorage\Storage as FilterStorage;
use FDevs\Fixture\Storage\StorageInterface;
use Psr\Cache\CacheItemPoolInterface;

class Storage extends FilterStorage
{
    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var array [`storedItemKey` => `identitiesStorageKey`]
     */
    private $identitiesKeys = [];

    /**
     * Storage constructor.
     *
     * @param StorageInterface       $storage
     * @param EntityManagerInterface $manager
     */
    public function __construct(
        EntityManagerInterface $manager,
        CacheItemPoolInterface $cachePool,
        FilterInterface $filter = null,
        string $keyPrefix = '',
        int $ttl = null
    ) {
        parent::__construct($cachePool, $filter, $keyPrefix, $ttl);
        $this->manager = $manager;
    }

    /**
     * @inheritDoc
     */
    public function store($object, string $type): string
    {
        $key = parent::store($object, $type);

        if ($this->hasIdentifier($object)) {
            $identities = $this->getIdentifier($object);

            $className = \get_class($object);
            $type = \md5($className . ':identities');
            $identitiesKey = parent::store($identities, $type);

            $this->identitiesKeys[$key] = $identitiesKey;
        }

        return $key;
    }

    /**
     * @inheritDoc
     */
    public function get(string $key)
    {
        $object = parent::get($key);
        $object = $this->prepareStoredObject($key, $object);

        return $object;
    }

    /**
     * @param object $object
     *
     * @return bool
     */
    private function hasIdentifier($object): bool
    {
        $uow = $this->manager->getUnitOfWork();

        return $uow->isInIdentityMap($object);
    }
    /**
     * Get identifier for a unit of work
     *
     * @param object $object Reference object
     *
     * @return array
     */
    private function getIdentifier($object): array
    {
        if (!$this->hasIdentifier($object)) {
            $class = $this->manager->getClassMetadata(\get_class($object));

            return $class->getIdentifierValues($object);
        }

        $uow = $this->manager->getUnitOfWork();
        return $uow->getEntityIdentifier($object);
    }

    /**
     * @param string $key
     * @param object $object
     *
     * @throws ORMException
     *
     * @return object
     */
    private function prepareStoredObject(string $key, $object)
    {
        $identitiesKey = $this->identitiesKeys[$key];
        if (!$this->manager->contains($object) && $this->has($identitiesKey)) {
            $meta = $this->manager->getClassMetadata(\get_class($object));
            $identities = parent::get($identitiesKey);
            $reference = $this->manager->getReference(
                $meta->name,
                $identities
            );
            $object = $reference;
        }

        return $object;
    }
}
