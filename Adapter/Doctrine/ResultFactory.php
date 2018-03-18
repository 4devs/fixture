<?php

namespace FDevs\Fixture\Adapter\Doctrine;


use Doctrine\Common\DataFixtures\FixtureInterface;
use FDevs\Executor\ResultInterface;

class ResultFactory implements ResultFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function create(FixtureInterface $fixture): ResultInterface
    {
        return new Result($fixture);
    }
}