<?php

namespace FDevs\Fixture\Adapter\Doctrine;

use Doctrine\Common\DataFixtures\FixtureInterface as DoctrineFixture;
use Doctrine\Common\Persistence\ObjectManager;
use FDevs\Fixture\FixtureInterface;

class FixtureFactory implements FixtureFactoryInterface
{
    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * FixtureFactory constructor.
     * @param ResultFactoryInterface $resultFactory
     */
    public function __construct(ResultFactoryInterface $resultFactory)
    {
        $this->resultFactory = $resultFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create(DoctrineFixture $fixture, ObjectManager $manager, array $dependencies = []): FixtureInterface
    {
        return new Fixture($fixture, $manager, $this->resultFactory, $dependencies);
    }
}
