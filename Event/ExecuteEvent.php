<?php

namespace FDevs\Fixture\Event;

use Symfony\Component\EventDispatcher\Event;

class ExecuteEvent extends Event
{
    /**
     * @var array
     */
    private $context;
    /**
     * @var array
     */
    private $fixtures;

    /**
     * ExecuteEvent constructor.
     *
     * @param array $context   [`name` => value]
     * @param string[]         $fixtures Array of fixtures service ids to execute
     */
    public function __construct(array $context, array $fixtures)
    {
        $this->context = $context;
        $this->fixtures = $fixtures;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @return array
     */
    public function getFixtures(): array
    {
        return $this->fixtures;
    }
}
