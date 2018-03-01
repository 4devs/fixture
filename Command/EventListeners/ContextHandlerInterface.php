<?php

namespace FDevs\Fixture\Command\EventListeners;

use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;

interface ContextHandlerInterface
{
    /**
     * Add context options into input definition
     *
     * @param InputDefinition $def
     *
     * @return ContextHandlerInterface
     */
    public function configureOptions(InputDefinition $def): self;

    /**
     * Create command context from input options
     *
     * @param InputInterface $input
     *
     * @return array    [`name` => value]
     */
    public function buildContext(InputInterface $input): array;
}
