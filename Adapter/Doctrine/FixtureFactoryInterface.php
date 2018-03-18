<?php

namespace FDevs\Fixture\Adapter\Doctrine;


use Doctrine\Common\DataFixtures\FixtureInterface as DoctrineFixture;
use Doctrine\Common\Persistence\ObjectManager;
use FDevs\Fixture\FixtureInterface;

interface FixtureFactoryInterface
{
    /**
     * @param DoctrineFixture $fixture
     * @param ObjectManager $manager
     * @param array $dependencies
     *
     * @return FixtureInterface
     */
    public function create(DoctrineFixture $fixture, ObjectManager $manager, array $dependencies = []): FixtureInterface;
}