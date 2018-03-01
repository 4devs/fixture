<?php

namespace FDevs\Fixture\Command;

use FDevs\Executor\ExecutorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LoadCommand extends Command
{
    private const OPTION_FIXTURE = 'fixture';

    /**
     * @var ExecutorInterface
     */
    private $executor;

    /**
     * @var array
     */
    private $context;

    /**
     * ExecuteCommand constructor.
     *
     * @param ExecutorInterface           $executor
     * @param string|null                 $name
     *
     * @throws LogicException
     */
    public function __construct(
        ExecutorInterface $executor,
        string $name = 'fdevs:fixture:load'
    ) {
        parent::__construct($name);
        $this->executor = $executor;

        $this->resetContext();
    }

    /**
     * @param array $context [`name` => value]
     *
     * @return LoadCommand
     */
    public function setContext(array $context): self
    {
        $this->context = $context;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->addOption(
                self::OPTION_FIXTURE,
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'Fixture to load. If empty, executes all fixtures',
                []
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $fixtures = $input->getOption(self::OPTION_FIXTURE);
            $resultIterator = $this->executor->execute($this->context, $fixtures);
            foreach ($resultIterator as $result) {
                $this->writeLn($output, 'info', (string) $result);
            }
            $this->resetContext();
        } catch (\Throwable $e) {
            $this
                ->resetContext()
                ->writeLn($output, 'error', $e->getMessage())
            ;
            throw $e;
        }
    }

    /**
     * @return LoadCommand
     */
    private function resetContext(): self
    {
        $this->context = [];

        return $this;
    }

    /**
     * Console output message by defined type
     *
     * @param OutputInterface $output
     * @param string          $type
     * @param string          $msg
     *
     * @return LoadCommand
     */
    private function writeLn(OutputInterface $output, string $type, string $msg): self
    {
        $output->writeln('<' . $type . '>' . $msg . '</' . $type . '>');

        return $this;
    }
}
