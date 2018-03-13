<?php

namespace FDevs\Fixture;

final class FDevsFixtureEvents
{
    /**
     * The EXCEPTION_EXECUTE event occurs on execute exception.
     * The event listener method receives a FDevs\Fixture\Event\ExecuteExceptionEvent instance.
     *
     * @Event
     *
     * @var string
     */
    public const EXCEPTION_EXECUTE = 'f_devs_fixture.executor.exception_execute';

    /**
     * The POST_EXECUTE event occurs after execute fixtures.
     * The event listener method receives a FDevs\Fixture\Event\ExecuteEvent instance.
     *
     * @Event
     *
     * @var string
     */
    public const POST_EXECUTE = 'f_devs_fixture.executor.post_execute';
    /**
     * The PRE_EXECUTE event occurs before execute fixtures.
     * The event listener method receives a FDevs\Fixture\Event\ExecuteEvent instance.
     *
     * @Event
     *
     * @var string
     */
    public const PRE_EXECUTE = 'f_devs_fixture.executor.pre_execute';
}
