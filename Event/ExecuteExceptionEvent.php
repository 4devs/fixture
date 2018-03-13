<?php

namespace FDevs\Fixture\Event;

class ExecuteExceptionEvent extends ExecuteEvent
{
    /**
     * @var \Throwable
     */
    private $exception;

    /**
     * ExecuteEvent constructor.
     *
     * @param array      $context  [`option_name` => value]
     * @param string[]   $fixtures Array of fixtures service ids to execute
     * @param \Throwable $exception
     */
    public function __construct(array $context, array $fixtures, \Throwable $exception)
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
