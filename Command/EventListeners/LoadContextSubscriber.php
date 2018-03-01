<?php

namespace FDevs\Fixture\Command\EventListeners;

use FDevs\Fixture\Command\LoadCommand;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoadContextSubscriber implements EventSubscriberInterface
{
    /**
     * @var ContextHandlerInterface
     */
    private $contextHandler;

    /**
     * LoadContextListener constructor.
     *
     * @param ContextHandlerInterface $contextHandler
     */
    public function __construct(ContextHandlerInterface $contextHandler)
    {
        $this->contextHandler = $contextHandler;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ConsoleEvents::COMMAND => ['applyCommandContext'],
        ];
    }

    /**
     * Add context from input options for FDevs\Fixture\Command\LoadCommand
     *
     * @param ConsoleCommandEvent $event
     */
    public function applyCommandContext(ConsoleCommandEvent $event): void
    {
        $command = $event->getCommand();
        if (!$command instanceof LoadCommand) {
            return;
        }

        $inputDef = $command->getDefinition();
        $this->contextHandler->configureOptions($inputDef);

        $input = new ArgvInput(null, $inputDef);
        $context = $this->contextHandler->buildContext($input);
        $command->setContext($context);
    }
}
