<?php

namespace FDevs\Fixture\Storage;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

class StorageAggregator extends ServiceLocator implements StorageAggregatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function store(string $type, $data, string $key = null): StorageAggregatorInterface
    {
        $this->getStorage($type)->store($data, $key);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReferences(string $type, array $options): array
    {
        return $this->getStorage($type)->getReferences($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenceByKey(string $type, string $key)
    {
        return $this->getStorage($type)->getReferenceByKey($key);
    }

    /**
     * @param string $type
     *
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     *
     * @return StorageInterface
     */
    private function getStorage(string $type): StorageInterface
    {
        return $this->get($type);
    }
}
