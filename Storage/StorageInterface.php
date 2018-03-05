<?php

namespace FDevs\Fixture\Storage;

use FDevs\Fixture\Exception\Storage\NotFoundException;
use FDevs\Fixture\Exception\Storage\StoreException;

interface StorageInterface
{
    /**
     * @param mixed         $data
     * @param string        $type
     *
     * @throws StoreException
     *
     * @return string   Key of stored item
     */
    public function store($data, string $type): string;

    /**
     * @param string $type
     * @param array  $options
     *
     * @return \Iterator    Iterator of stored data by options
     */
    public function find(string $type, array $options): \Iterator;

    /**
     * @param string $key
     *
     * @throws NotFoundException
     *
     * @return mixed
     */
    public function get(string $key);
}
