<?php

namespace FDevs\Fixture\Adapter\Doctrine;


use Doctrine\Common\DataFixtures\FixtureInterface;
use FDevs\Executor\ResultInterface;

class Result implements ResultInterface
{
    /**
     * @var FixtureInterface
     */
    private $fixture;

    /**
     * Result constructor.
     *
     * @param FixtureInterface $fixture
     */
    public function __construct(FixtureInterface $fixture)
    {
        $this->fixture = $fixture;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return 'Fixture "' . \get_class() . '" completed';
    }

    /**
     * @return FixtureInterface
     */
    public function getFixture(): FixtureInterface
    {
        return $this->fixture;
    }
}