<?php

namespace FDevs\Fixture\Command;

use FDevs\Executor\ContextInterface;
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
     * @param ContextInterface $context
     * @param InputInterface   $input
     *
     * @return ContextHandlerInterface
     */
    public function extendContext(ContextInterface $context, InputInterface $input): self;
}
