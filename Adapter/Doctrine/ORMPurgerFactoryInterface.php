<?php

namespace FDevs\Fixture\Adapter\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use FDevs\Fixture\PurgerInterface;

interface ORMPurgerFactoryInterface
{
    /**
     * @param EntityManagerInterface $em
     * @param array                  $exclude
     *
     * @return PurgerInterface
     */
    public function create(EntityManagerInterface $em, array $exclude = []): PurgerInterface;
}
