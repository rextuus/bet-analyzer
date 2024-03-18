<?php

namespace App\Command\Tipico;

use App\Service\Tipico\Message\InitSimulatorProcessingMessage;
use App\Service\Tipico\Message\SimulatorProcessBulk;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'ProcessSimulators',
    description: 'Add a short description for your command',
)]
class ProcessSimulatorsCommand extends Command
{
    public function __construct(private readonly MessageBusInterface $messageBus)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('variant', InputArgument::REQUIRED, 'Argument description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $variant = $input->getArgument('variant');

        $bulk = SimulatorProcessBulk::tryFrom($variant);
        if (!$bulk) {
            throw new \Exception('Invlaid SimulatorProcessBulk');
        }

        $message = new InitSimulatorProcessingMessage($bulk);
        $this->messageBus->dispatch($message);

        return Command::SUCCESS;
    }
}
