<?php

namespace FDevs\Fixture\Event;

use FDevs\Executor\ContextInterface;
use Symfony\Component\EventDispatcher\Event;

class ExecuteEvent extends Event
{
    /**
     * @var ContextInterface
     */
    private $context;
    /**
     * @var array
     */
    private $fixtures;

    /**
     * ExecuteEvent constructor.
     *
     * @param ContextInterface $context
     * @param string[]         $fixtures Array of fixtures service ids to execute
     */
    public function __construct(ContextInterface $context, array $fixtures)
    {
        $this->context = $context;
        $this->fixtures = $fixtures;
    }

    /**
     * @return ContextInterface
     */
    public function getContext(): ContextInterface
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
