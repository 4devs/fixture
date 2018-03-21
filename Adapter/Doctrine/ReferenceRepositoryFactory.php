<?php

namespace FDevs\Fixture\Adapter\Doctrine;

use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\Common\Persistence\ObjectManager;

class ReferenceRepositoryFactory implements ReferenceRepositoryFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function create(ObjectManager $manager): ReferenceRepository
    {
        return new ReferenceRepository($manager);
    }
}
