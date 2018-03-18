<?php

namespace FDevs\Fixture\Adapter\Doctrine;


use Doctrine\Common\DataFixtures\FixtureInterface as DoctrineFixture;
use Doctrine\Common\Persistence\ObjectManager;
use FDevs\Executor\ResultInterface;
use FDevs\Fixture\DependentFixtureInterface;

class Fixture implements DependentFixtureInterface
{
    /**
     * @var DoctrineFixture
     */
    private $fixture;

    /**
     * @var ObjectManager
     */
    private $manager;
    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;
    /**
     * @var array
     */
    private $dependencies;

    /**
     * Fixture constructor.
     *
     * @param DoctrineFixture $fixture
     * @param ObjectManager $manager
     * @param ResultFactoryInterface $resultFactory
     * @param array $dependencies
     */
    public function __construct(
        DoctrineFixture $fixture,
        ObjectManager $manager,
        ResultFactoryInterface $resultFactory,
        array $dependencies = []
    ) {
        $this->fixture = $fixture;
        $this->manager = $manager;
        $this->resultFactory = $resultFactory;
        $this->dependencies = $dependencies;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(array $context): ResultInterface
    {
        $this->fixture->load($this->manager);

        return $this->resultFactory->create($this->fixture);
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }
}