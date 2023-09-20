<?php
declare(strict_types=1);

namespace App\Service\Evaluation;

use App\Entity\BetRowOddFilter;
use App\Entity\SpmFixture;
use App\Entity\SpmOdd;
use App\Entity\SpmScore;
use App\Form\InitSimpleBetRowsForSeasonData;
use App\Service\Evaluation\Content\BetRow\BetRowInterface;
use App\Service\Evaluation\Content\BetRow\SimpleBetRow\Data\SimpleBetRowData;
use App\Service\Evaluation\Content\BetRow\SimpleBetRow\SimpleBetRowService;
use App\Service\Evaluation\Content\BetRowOddFilter\BetRowOddFilterService;
use App\Service\Evaluation\Content\BetRowOddFilter\Data\BetRowOddFilterData;
use App\Service\Evaluation\Content\PlacedBet\BetRowVariant;
use App\Service\Evaluation\Content\PlacedBet\Data\PlacedBetData;
use App\Service\Evaluation\Content\PlacedBet\PlacedBetService;
use App\Service\Evaluation\Message\InitBetRowMessage;
use App\Service\Evaluation\Message\PlaceBetMessage;
use App\Service\Sportmonks\Content\Fixture\SpmFixtureService;
use App\Service\Sportmonks\Content\Odd\SpmOddService;
use App\Service\Sportmonks\Content\Round\SpmRoundService;
use App\Service\Sportmonks\Content\Score\SpmScoreService;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class BetRowCalculator
{

    public function __construct(
        private readonly PlacedBetService       $placedBetService,
        private readonly SpmFixtureService      $fixtureService,
        private readonly SpmOddService          $oddService,
        private readonly SpmScoreService        $scoreService,
        private readonly SpmRoundService        $spmRoundService,
        private readonly SimpleBetRowService    $simpleBetRowService,
        private readonly BetRowOddFilterService $betRowOddFilterService,
        private readonly MessageBusInterface    $bus,

    )
    {
    }

    public function initClassicBetRowSetForSeason(InitSimpleBetRowsForSeasonData $data): void
    {
        $betOnVariants = [BetOn::HOME, BetOn::DRAW, BetOn::AWAY];
        $steps = $this->calculateSteps($data);
        foreach ($betOnVariants as $betOnVariant){
            foreach ($steps as $step){
                $message = new InitBetRowMessage($data, $betOnVariant, $step['from'], $step['to']);
                $this->bus->dispatch($message);
            }
        }
    }

    private function calculateSteps(InitSimpleBetRowsForSeasonData $data): array
    {
        $steps = [];
        $to = $data->getMin() + $data->getSteps();
        for ($from = $data->getMin(); $from <= $data->getMax() - $data->getSteps(); $from = $from + $data->getSteps()){
            $steps[] = ['from' => $from, 'to' => $to];
            $to = $to + $data->getSteps();
        }
        return $steps;
    }

    public function prepareInitBetRow(InitSimpleBetRowsForSeasonData $data, BetOn $betOnVariant, float $from, float $to): void
    {
        // create the specific filter or use existing one
        $filterData = new BetRowOddFilterData();
        $filterData->setMin($from);
        $filterData->setMax($to);
        $filterData->setOddVariant(OddVariant::CLASSIC_3_WAY);
        $filterData->setBetOn($betOnVariant);
        $filters = $this->betRowOddFilterService->findBy(
            [
                'min' => $from,
                'max' => $to,
                'oddVariant' => OddVariant::CLASSIC_3_WAY,
                'betOn' => $betOnVariant,
            ]
        );
        if (!count($filters)){
            $filter = $this->betRowOddFilterService->createByData($filterData);
        }else{
            $filter = $filters[0];
        }

        // check if combination may already exist
        $exists = $this->simpleBetRowService->findBySeasonAndFilter($data->getSeason(), $filter);
        if ($exists){
            dump("Already exists");
            return;
        }

        // create new bet row
        $betRowData = new SimpleBetRowData();
        $betRowData->setWager($data->getWager());
        $betRowData->setVariant(BetRowVariant::SimpleBetRow);
        $betRowData->setCashBox($data->getInitialCashBox());
        $betRowData->setSeasonApiId($data->getSeason()->getApiId());
        $betRowData->setLeagueApiId($data->getSeason()->getLeagueApiId());
        $betRowData->setOddAccumulationVariant($data->getOddAccumulationVariant());
        $betRowData->setIncludeTaxes($data->isIncludeTax());
        $betRowData->setFilters([$filter]);
        $simpleBetRow = $this->simpleBetRowService->createByData($betRowData);


        $data2 = new ClassicBetRowCalculatorInitData();
        $data2->setBetRow($simpleBetRow);
        $data2->setSeason($data->getSeason());
        $data2->setOddFilter([$filter]);

        $data2->setAccumulationVariant($data->getOddAccumulationVariant());
        $data2->setVariant(BetRowVariant::SimpleBetRow);
        $data2->setIncludeTax($data->isIncludeTax());
        $data2->setWager($data->getWager());

        $this->dispatchCalculationMessagesForBetRow($data2);
    }

    public function dispatchCalculationMessagesForBetRow(ClassicBetRowCalculatorInitData $betRowCalculatorInitData): void
    {
        $season = $betRowCalculatorInitData->getSeason();
        $fixtures = $this->fixtureService->findBy(['seasonApiId' => $season->getApiId()]);

        foreach ($fixtures as $fixture) {
            // create a message for each fixture/odd variant combination 300-400 x 1-10 messages per bet row (depending on the odd range)
            foreach ($betRowCalculatorInitData->getOddFilter() as $filter) {
                $messages = new PlaceBetMessage(
                    $betRowCalculatorInitData->getAccumulationVariant(),
                    $fixture->getApiId(),
                    $filter->getId(),
                    $betRowCalculatorInitData->getBetRow()->getId(),
                    $betRowCalculatorInitData->getVariant(),
                    $betRowCalculatorInitData->getWager(),
                    $betRowCalculatorInitData->isIncludeTax()
                );
                $this->bus->dispatch($messages);

            }
        }
    }


    /**
     * @param SpmOdd[] $mappedOdds
     * @return BetAccumulation
     */
    private function calculateBetAccumulation(array $mappedOdds): BetAccumulation
    {
        $betStatistic = new BetAccumulation();

        usort(
            $mappedOdds,
            function (SpmOdd $a, SpmOdd $b) {
                return $a->getValue() >= $b->getValue();
            }
        );
        $median = $mappedOdds[count($mappedOdds) / 2];
        $total = 0.0;

        foreach ($mappedOdds as $odd) {
            $total = $total + $odd->getValue();
        }

        $betStatistic->setMinOdd($mappedOdds[0]->getValue());
        $betStatistic->setMaxOdd($mappedOdds[array_key_last($mappedOdds)]->getValue());
        $betStatistic->setAvgOdd($total / count($mappedOdds));
        $betStatistic->setMedianOdd($median->getValue());

        $ids = array_map(
            function (SpmOdd $odd) {
                return $odd->getId();
            },
            $mappedOdds
        );
        $betStatistic->setOddIds($ids);

        return $betStatistic;
    }

    /**
     * @param SpmFixture $fixture
     * @param PlacedBetData $placedBetData
     * @param BetRowOddFilter $filter
     * @param bool $includeTax
     * @return void
     * @throws NoScoresFoundForFixtureException
     */
    public function checkMatchOutcome(
        SpmFixture      $fixture,
        PlacedBetData   $placedBetData,
        BetRowOddFilter $filter,
        bool            $includeTax = true
    ): void
    {
        $scores = $this->scoreService->findBy(['fixtureApiId' => $fixture->getId()]);
        if (!count($scores)) {
            throw new NoScoresFoundForFixtureException('Fixture id: ' . $fixture->getId());
        }

        $home = 0;
        $away = 0;
        foreach ($scores as $score) {
            if ($score->getDescription() === SpmScore::SECOND_HALF && $score->getParticipant() === SpmScore::PARTICIPANT_HOME) {
                $home = $score->getGoals();
            }
            if ($score->getDescription() === SpmScore::SECOND_HALF && $score->getParticipant() === SpmScore::PARTICIPANT_AWAY) {
                $away = $score->getGoals();
            }
        }
        $result = BetOn::DRAW;

        if ($home > $away) {
            $result = BetOn::HOME;
        }
        if ($home < $away) {
            $result = BetOn::AWAY;
        }

        $placedBetData->setWon($result === $filter->getBetOn());
        $output = $placedBetData->getWager() * -1;
        if ($placedBetData->isWon()) {
            $totalWin = $placedBetData->getWager() * $placedBetData->getOdd();
            $tax = 0.0;
            if ($includeTax) {
                $tax = $totalWin * 0.05;
            }
            $output = $totalWin - $tax;
        }

        $placedBetData->setOutput($output);
    }

    /**
     * @throws NoScoresFoundForFixtureException
     */
    public function placeBet(
        SpmFixture             $fixture,
        BetRowOddFilter        $filter,
        OddAccumulationVariant $accumulationVariant,
        BetRowInterface        $betRow,
        BetRowVariant          $betRowVariant,
        float                  $wager,
        bool                   $includeTax,
    ): void
    {
        $odds = $this->oddService->findByFixtureAndVariant($fixture, $filter);

        if (count($odds)) {
            $accumulation = $this->calculateBetAccumulation($odds);

            $placedBetData = new PlacedBetData();
            $placedBetData->setBetRow($betRow);
            $placedBetData->setVariant($betRowVariant);
            $placedBetData->setOddApiIds($accumulation->getOddIds());
            $placedBetData->setWager($wager);
            $placedBetData->setFixtureApiId($fixture->getId());

            // choose the odd
            $getter = match ($accumulationVariant) {
                OddAccumulationVariant::MEDIAN => 'getMedianOdd',
                OddAccumulationVariant::MIN => 'getMinOdd',
                OddAccumulationVariant::MAX => 'getMaxOdd',
                OddAccumulationVariant::AVG => 'getAvgOdd',
            };
            $placedBetData->setOdd($accumulation->{$getter}());

            $matchDay = -1;
            $round = $this->spmRoundService->findById($fixture->getApiId());
            if ($round) {
                $matchDay = (int)$round->getName();
            }

            $placedBetData->setMatchDay($matchDay);

            $this->checkMatchOutcome($fixture, $placedBetData, $filter, $includeTax);

            $this->placedBetService->createByData($placedBetData);
        }
    }
}
