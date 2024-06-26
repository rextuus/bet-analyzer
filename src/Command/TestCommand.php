<?php

namespace App\Command;

use App\Service\BettingProvider\Betano\Api\BetanoApiGateway;
use App\Service\BettingProvider\Betano\Content\BetanoBet\BetanoBetService;
use App\Service\BettingProvider\BettingProviderBackupFile\BackupProcessorTargetEntityServiceProvider;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\SimulationStatisticService;
use App\Service\Tipico\TipicoBetSimulationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'test',
    description: 'Add a short description for your command',
)]
class TestCommand extends Command
{


    public function __construct(
        private readonly TipicoBetSimulationService $betSimulationService,
        private readonly SimulatorService $simulatorService,
        private readonly MessageBusInterface $messageBus,
        private readonly BetanoApiGateway $apiGateway,
        private readonly BetanoBetService $betanoBetService,
        private readonly BackupProcessorTargetEntityServiceProvider $serviceProvider,
        private readonly SimulationStatisticService $simulationStatisticService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->simulationStatisticService->calculateAverageIncreaseScore();

        dump($result);

        return Command::SUCCESS;
    }
}
