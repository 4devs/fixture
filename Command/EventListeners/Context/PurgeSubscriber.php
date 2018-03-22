<?php

namespace FDevs\Fixture\Command\EventListeners\Context;

use FDevs\Fixture\Command\ContextHandler\PurgeHandler;
use FDevs\Fixture\Event\ExecuteEvent;
use FDevs\Fixture\FDevsFixtureEvents;
use FDevs\Fixture\PurgerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PurgeSubscriber implements EventSubscriberInterface
{
    /**
     * @var PurgerInterface
     */
    private $purger;

    /**
     * PurgeSubscriber constructor.
     *
     * @param PurgerInterface $purger
     */
    public function __construct(PurgerInterface $purger)
    {
        $this->purger = $purger;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            FDevsFixtureEvents::PRE_EXECUTE => ['processPurge'],
        ];
    }

    /**
     * @param ExecuteEvent $event
     *
     * @return PurgeSubscriber
     */
    public function processPurge(ExecuteEvent $event): self
    {
        if ($event->getContext()[PurgeHandler::OPTION_PURGE]) {
            $this->purger->purge($event->getContext());
        }

        return $this;
    }
}
