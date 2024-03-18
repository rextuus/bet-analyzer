<?php

namespace App\Command;

use App\Entity\BetRowSummary;
use App\Entity\SimpleBetRow;
use App\Entity\SpmSeason;
use App\Form\InitSimpleBetRowsForSeasonData;
use App\Service\Evaluation\BetRowCalculator;
use App\Service\Evaluation\Content\BetRow\SimpleBetRow\SimpleBetRowRepository;
use App\Service\Evaluation\Content\BetRow\SimpleBetRow\SimpleBetRowService;
use App\Service\Evaluation\Content\BetRowOddFilter\BetRowOddFilterService;
use App\Service\Evaluation\Message\TriggerBetRowsForSeasonMessage;
use App\Service\Evaluation\OddAccumulationVariant;
use App\Service\Sportmonks\Api\SportsmonkApiGateway;
use App\Service\Sportmonks\Api\SportsmonkService;
use App\Service\Sportmonks\Content\League\SpmLeagueService;
use App\Service\Sportmonks\Content\Season\SpmSeasonService;
use App\Service\Statistic\SeasonBetRowMap;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\Message\InitSimulatorProcessingMessage;
use App\Service\Tipico\Message\SimulatorProcessBulk;
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
//        $sim = $this->simulatorService->findBy(['id' => 1])[0];
//        $this->betSimulationService->simulate($sim);

        $this->messageBus->dispatch(new InitSimulatorProcessingMessage(SimulatorProcessBulk::OVER_UNDER_SIMULATORS));
        return Command::SUCCESS;
    }
}
