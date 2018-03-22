<?php

namespace FDevs\Fixture\Command\ContextHandler;

use FDevs\Fixture\Command\ContextHandlerInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class PurgeHandler implements ContextHandlerInterface
{
    public const OPTION_PURGE = 'purge';
    public const OPTION_PURGE_WITH_TRUNCATE = 'purge-with-truncate';

    /**
     * @inheritDoc
     */
    public function configureOptions(InputDefinition $def): ContextHandlerInterface
    {
        $def
            ->addOption(new InputOption(
                self::OPTION_PURGE,
                null,
                InputOption::VALUE_NONE,
                'Purge all data before fixture execute.'
            ))
        ;
        $def
            ->addOption(new InputOption(
                self::OPTION_PURGE_WITH_TRUNCATE,
                null,
                InputOption::VALUE_NONE,
                'Truncate storage or delete otherwise.'
            ))
        ;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function buildContext(InputInterface $input, array $context = []): array
    {
        $context[self::OPTION_PURGE] = $input->getOption(self::OPTION_PURGE);
        $context[self::OPTION_PURGE_WITH_TRUNCATE] = $input->getOption(self::OPTION_PURGE_WITH_TRUNCATE);

        return $context;
    }
}
