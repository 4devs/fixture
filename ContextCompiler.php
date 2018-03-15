<?php

namespace FDevs\Fixture;

use FDevs\Executor\ContextInterface;
use FDevs\Fixture\Command\ContextHandlerInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;

class ContextCompiler implements ContextHandlerInterface
{
    /**
     * @var iterable|ContextHandlerInterface[]
     */
    private $handlers;

    /**
     * ContextCompiler constructor.
     *
     * @param iterable|ContextHandlerInterface[] $handlers
     */
    public function __construct(iterable $handlers)
    {
        $this->handlers = $handlers;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(InputDefinition $def): ContextHandlerInterface
    {
        $this->proceedHandlers(function (ContextHandlerInterface $handler) use ($def) {
            $handler->configureOptions($def);
        });

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function extendContext(ContextInterface $context, InputInterface $input): ContextHandlerInterface
    {
        $this->proceedHandlers(function (ContextHandlerInterface $handler) use ($context, $input) {
            $handler->extendContext($context, $input);
        });

        return $this;
    }

    /**
     * @param callable $func
     *
     * @return ContextCompiler
     */
    private function proceedHandlers(callable $func): self
    {
        foreach ($this->handlers as $handler) {
            $func($handler);
        }

        return $this;
    }
}
