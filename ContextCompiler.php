<?php

namespace FDevs\Fixture;

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
    public function buildContext(InputInterface $input, array $context = []): array
    {
        $this->proceedHandlers(function (ContextHandlerInterface $handler) use (&$context, $input) {
            $context = $handler->buildContext($input, $context);
        });

        return $context;
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
