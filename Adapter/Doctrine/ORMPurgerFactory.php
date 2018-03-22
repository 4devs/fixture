<?php

namespace FDevs\Fixture\Adapter\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use FDevs\Fixture\PurgerInterface;

class ORMPurgerFactory implements ORMPurgerFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function create(EntityManagerInterface $em, array $exclude = []): PurgerInterface
    {
        return new ORMPurger($em, $exclude);
    }
}
