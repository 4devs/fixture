<?php

namespace FDevs\Fixture;


use FDevs\Executor\DependentExecutableInterface;

interface DependentFixtureInterface extends FixtureInterface, DependentExecutableInterface
{
}