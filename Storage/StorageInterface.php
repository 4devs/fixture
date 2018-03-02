<?php

namespace FDevs\Fixture\Storage;

use FDevs\Fixture\Exception\Storage\KeyDefinedException;
use FDevs\Fixture\Exception\Storage\NotFoundException;

interface StorageInterface
{
    /**
     * Store object
     *
     * @param             $data
     * @param string|null $key
     *
     * @throws KeyDefinedException
     *
     * @return StorageInterface
     */
    public function store($data, string $key = null): self;

    /**
     * Return array of stored $data corresponding to the options.
     *
     * @param array       $options
     *
     * @return array
     */
    public function getReferences(array $options): array;

    /**
     * @param string $key
     *
     * @throws NotFoundException
     *
     * @return mixed
     */
    public function getReferenceByKey(string $key);
}
