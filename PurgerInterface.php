<?php

namespace FDevs\Fixture;

interface PurgerInterface
{
    /**
     * @param array $context    [`name` => value]
     *
     * @return PurgerInterface
     */
    public function purge(array $context): PurgerInterface;
}
