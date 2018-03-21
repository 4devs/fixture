<?php

namespace FDevs\Fixture\Adapter\Doctrine;

use Doctrine\Common\DataFixtures\FixtureInterface;
use FDevs\Executor\ResultInterface;

interface ResultFactoryInterface
{
    /**
     * @param FixtureInterface $fixture
     *
     * @return ResultInterface
     */
    public function create(FixtureInterface $fixture): ResultInterface;
}
