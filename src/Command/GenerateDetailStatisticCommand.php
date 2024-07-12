<?php

namespace App\Command;

use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\Statistic\DetailStatisticService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'GenerateDetailStatistic',
    description: 'Add a short description for your command',
)]
class GenerateDetailStatisticCommand extends Command
{
    public function __construct(
        private SimulatorService $simulatorService,
        private DetailStatisticService $statisticService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('simulatorId', InputArgument::REQUIRED, 'Argument description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $simulatorId = $input->getArgument('simulatorId');

        $simulator = $this->simulatorService->find($simulatorId);
        $this->statisticService->generateDetailStatisticForSimulator($simulator);
        $io->info(sprintf('Refresh detail statistic for simulator: %s', $simulator->getIdentifier()));

        return Command::SUCCESS;
    }
}
