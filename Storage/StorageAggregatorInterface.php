<?php

namespace FDevs\Fixture\Storage;

use FDevs\Fixture\Exception\Storage\KeyDefinedException;
use FDevs\Fixture\Exception\Storage\NotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

interface StorageAggregatorInterface
{
    /**
     * @param string      $type
     * @param             $data
     * @param string|null $key
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws KeyDefinedException
     *
     * @return StorageAggregatorInterface
     */
    public function store(string $type, $data, string $key = null): self;

    /**
     * Return array of stored $data corresponding to the options.
     *
     * @param string      $type
     * @param array       $options
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @return array
     */
    public function getReferences(string $type, array $options): array;

    /**
     * @param string $type
     * @param string $key
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     *
     * @return mixed
     */
    public function getReferenceByKey(string $type, string $key);
}
