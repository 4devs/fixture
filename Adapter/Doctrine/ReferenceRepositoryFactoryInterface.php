<?php

namespace FDevs\Fixture\Adapter\Doctrine;

use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\Common\Persistence\ObjectManager;

interface ReferenceRepositoryFactoryInterface
{
    /**
     * @param ObjectManager $manager
     *
     * @return ReferenceRepository
     */
    public function create(ObjectManager $manager): ReferenceRepository;
}
