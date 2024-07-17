<?php

namespace App\Command;

use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\Message\CreateOrUpdateDetailStatisticMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'GenerateDetailStatistic',
    description: 'Add a short description for your command',
)]
class GenerateDetailStatisticCommand extends Command
{
    public function __construct(
        private readonly SimulatorService $simulatorService,
        private readonly MessageBusInterface $bus
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('simulatorId', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'Start calculation for all');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $simulatorId = $input->getArgument('simulatorId');

        $all = $input->getOption('all');
        if ($all) {
            foreach ($this->simulatorService->findAllSimulatorIds() as $simulator) {
                $message = new CreateOrUpdateDetailStatisticMessage($simulator['id']);
                $this->bus->dispatch($message);
            }
            return Command::SUCCESS;
        }

        $message = new CreateOrUpdateDetailStatisticMessage($simulatorId);
        $this->bus->dispatch($message);

        $simulator = $this->simulatorService->find($simulatorId);
        $io->info(sprintf('Refresh detail statistic for simulator: %s', $simulator->getIdentifier()));

        return Command::SUCCESS;
    }
}
