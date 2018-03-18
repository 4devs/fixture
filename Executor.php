<?php

namespace FDevs\Fixture;

use FDevs\Executor\ExecutorInterface;
use FDevs\Fixture\Event\ExecuteEvent;
use FDevs\Fixture\Event\ExecuteExceptionEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Executor implements ExecutorInterface
{
    /**
     * @var ExecutorInterface
     */
    private $executor;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * Executor constructor.
     *
     * @param ExecutorInterface        $executor
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(ExecutorInterface $executor, EventDispatcherInterface $dispatcher)
    {
        $this->executor = $executor;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @inheritDoc
     */
    public function execute(array $context, array $fixtures = []): \Iterator
    {
        $event = new ExecuteEvent($context, $fixtures);

        $this->dispatcher->dispatch(FDevsFixtureEvents::PRE_EXECUTE, $event);

        try {
            yield from $this->executor->execute($context, $fixtures);
        } catch (\Throwable $e) {
            $this->dispatcher->dispatch(
                FDevsFixtureEvents::EXCEPTION_EXECUTE,
                new ExecuteExceptionEvent($context, $fixtures, $e)
            );
            throw $e;
        }

        $this->dispatcher->dispatch(FDevsFixtureEvents::POST_EXECUTE, $event);
    }
}
