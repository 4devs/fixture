<?php

namespace FDevs\Fixture\Event;

use FDevs\Executor\ContextInterface;

class ExecuteExceptionEvent extends ExecuteEvent
{
    /**
     * @var \Throwable
     */
    private $exception;

    /**
     * ExecuteEvent constructor.
     *
     * @param ContextInterface      $context
     * @param string[]   $fixtures Array of fixtures service ids to execute
     * @param \Throwable $exception
     */
    public function __construct(ContextInterface $context, array $fixtures, \Throwable $exception)
    {
        parent::__construct($context, $fixtures);
        $this->exception = $exception;
    }

    /**
     * @return \Throwable
     */
    public function getException(): \Throwable
    {
        return $this->exception;
    }
}
