<?php

namespace App\Command;

use App\Form\InitSimpleBetRowsForSeasonData;
use App\Service\Evaluation\BetOn;
use App\Service\Evaluation\BetRowCalculator;
use App\Service\Evaluation\ClassicBetRowCalculatorInitData;
use App\Service\Evaluation\Content\BetRow\SimpleBetRow\Data\SimpleBetRowData;
use App\Service\Evaluation\Content\BetRow\SimpleBetRow\SimpleBetRowRepository;
use App\Service\Evaluation\Content\BetRow\SimpleBetRow\SimpleBetRowService;
use App\Service\Evaluation\Content\BetRowOddFilter\BetRowOddFilterService;
use App\Service\Evaluation\Content\BetRowOddFilter\Data\BetRowOddFilterData;
use App\Service\Evaluation\Content\PlacedBet\BetRowVariant;
use App\Service\Evaluation\OddAccumulationVariant;
use App\Service\Evaluation\OddVariant;
use App\Service\Sportmonks\Api\SportsmonkApiGateway;
use App\Service\Sportmonks\Api\SportsmonkService;
use App\Service\Sportmonks\Content\League\SpmLeagueService;
use App\Service\Sportmonks\Content\Season\SpmSeasonService;
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
        private readonly SportsmonkService $sportsmonkService,
        private readonly SpmLeagueService $leagueService,
        private readonly SpmSeasonService $seasonService,
        private readonly SportsmonkApiGateway $apiGateway,
        private readonly BetRowCalculator $betRowCalculator,
        private readonly SimpleBetRowService $simpleBetRowService,
        private readonly BetRowOddFilterService $betRowOddFilterService,
        private readonly MessageBusInterface $bus,
        private readonly SimpleBetRowRepository $betRowRepository,
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

        $season = $this->seasonService->findBy(['apiId' => 18147])[0];

        $data = new InitSimpleBetRowsForSeasonData();
        $data->setSeason($season);
        $data->setWager(1.0);
        $data->setInitialCashBox(100.0);
        $data->setOddAccumulationVariant(OddAccumulationVariant::MEDIAN);
        $data->setMin(1.0);
        $data->setMax(1.5);
        $data->setSteps(0.5);

        $this->betRowCalculator->initClassicBetRowSetForSeason($data);

        // create a betRow for the whole shit
//        $betRowData = new SimpleBetRowData();
//        $betRowData->setWager(1.0);
//        $betRowData->setVariant(BetRowVariant::SimpleBetRow);
//        $betRowData->setCashBox(100.0);
//        $betRowData->setSeasonApiId($season->getApiId());
//        $betRowData->setLeagueApiId($season->getLeagueApiId());
//        $betRowData->setOddAccumulationVariant(OddAccumulationVariant::MEDIAN);
//        $betRowData->setIncludeTaxes(true);
//        $simpleBetRow = $this->simpleBetRowService->createByData($betRowData);
//
//        $filterData = new BetRowOddFilterData();
//        $filterData->setMin(1.0);
//        $filterData->setMax(2.0);
//        $filterData->setOddVariant(OddVariant::CLASSIC_3_WAY);
//        $filterData->setBetOn(BetOn::HOME);
//        $filterData->setBetRow($simpleBetRow);
//        $filter = $this->betRowOddFilterService->createByData($filterData);
//
//        $data = new ClassicBetRowCalculatorInitData();
//        $data->setBetRow($simpleBetRow);
//        $data->setSeason($season);
//
////        $oddFilterHome = new OddFilter();
////        $oddFilterHome->setVariant(OddVariant::CLASSIC_3_WAY);
////        $oddFilterHome->setBetOn(BetOn::HOME);
////        $oddFilterHome->setMinOdd(1.0);
////        $oddFilterHome->setMaxOdd(2.0);
////
////        $oddFilterAway = new OddFilter();
////        $oddFilterAway->setVariant(OddVariant::CLASSIC_3_WAY);
////        $oddFilterAway->setBetOn(BetOn::AWAY);
////        $oddFilterAway->setMinOdd(1.0);
////        $oddFilterAway->setMaxOdd(2.0);
//        $data->setOddFilter([$filter]);
//
//        $data->setAccumulationVariant(OddAccumulationVariant::MEDIAN);
//        $data->setVariant(BetRowVariant::SimpleBetRow);
//        $data->setIncludeTax(true);
//
//        $data->setWager(1.0);
//
//        dd($this->betRowCalculator->initClassicBetRow($data));
//        $response = $this->sportsmonkService->storeOddForFixture(235904);

        return Command::SUCCESS;
    }
}
