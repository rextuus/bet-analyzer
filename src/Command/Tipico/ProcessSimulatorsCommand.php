<?php

namespace App\Command\Tipico;

use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\Message\ProcessSimulatorMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'ProcessSimulators',
    description: 'Add a short description for your command',
)]
class ProcessSimulatorsCommand extends Command
{
    public function __construct(
        readonly private SimulatorService $simulatorService,
        private readonly MessageBusInterface $messageBus,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->simulatorService->findAll() as $simulator) {
            $message = new ProcessSimulatorMessage($simulator->getId());
            $this->messageBus->dispatch($message);
        }

        return Command::SUCCESS;
    }
}
