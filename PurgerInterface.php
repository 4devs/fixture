<?php

namespace FDevs\Fixture;

interface PurgerInterface
{
    /**
     * @return PurgerInterface
     */
    public function purge(): PurgerInterface;
}
