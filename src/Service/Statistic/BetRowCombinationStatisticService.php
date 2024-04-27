<?php
declare(strict_types=1);

namespace App\Service\Statistic;

use App\Entity\Spm\PlacedBet;
use App\Entity\Spm\SpmFixture;
use App\Service\Sportmonks\Content\Fixture\SpmFixtureService;
use App\Service\Sportmonks\Content\Season\SpmSeasonService;
use App\Service\Statistic\Content\BetRowCombination\BetRowCombinationService;


class BetRowCombinationStatisticService
{
    // BetRows should be marked and added/removed to a collection => entity
    // new form: only to choose which is the currently active BetRowCombination
    // calculate the daily made bets over all given rows => chart x: bet y: amount


    public function __construct(
        private BetRowCombinationService $betRowCombinationService,
        private SpmFixtureService $fixtureService,
        private SpmSeasonService $seasonService,
    )
    {
    }

    public function getBetRowCombinationStatistic(bool $includeDoubledPlacements = true): BetRowCombinationStatistic
    {
        $statistic = new BetRowCombinationStatistic();

        $activeCombination = $this->betRowCombinationService->getActiveCombination();

        // here we need to get only the betRow conditions first. Then we have to collect ALL Betrows fitting to this and work with this
        $betRows = $activeCombination->getBetRows()->toArray();

        $filter = [];
        $seasons = [];
        foreach ($betRows as $row){
            $currentFilter = $row->getBetRowFilters()->toArray()[0];
            $filter [$currentFilter->getId()] = $currentFilter;
        }

//        $betRows = $this->betRowService->findRowsWithFilter($filter);
//        dd($betRows);

        $betRowNames = [];
        $allBets = [];
        foreach ($betRows as $betRow) {
            $allBets = array_merge($allBets, $betRow->getPlacedBets()->toArray());

            $season = $this->seasonService->findBy(['apiId' => $betRow->getSeasonApiId()])[0];
            $name = sprintf(
                "%s %s",
                $season->getDisplayName(),
                (string) $betRow->getBetRowFilters()[0]
            );
            $betRowNames[] = $name;
        }

        $events = [];
        foreach ($allBets as $bet) {
            /** @var PlacedBet $bet */
            $fixture = $this->fixtureService->findByApiId($bet->getFixtureApiId());

            $this->addEvent($events, $fixture, $bet, $includeDoubledPlacements);
        }
        usort(
            $events,
            function (CombinationMadeBetEvent $a, CombinationMadeBetEvent $b) {
                return $a->getEventDate()->getTimestamp() > $b->getEventDate()->getTimestamp();
            }
        );

        $dates = [];
        $outComes = [];
        $madeBets = 0;
        $total = 0.0;
        foreach ($events as $event) {
            /** @var CombinationMadeBetEvent $event */
            $dates[] = $event->getEventDate();
            $outComes[] = $event->getOutcome() * $event->getOccurrence();
            $madeBets = $madeBets + $event->getOccurrence();
            $total = $total + $event->getOutcome() * $event->getOccurrence();
        }

        $statistic->setChartDataDays($dates);
        $statistic->setChartDataOutcomes($outComes);
        $statistic->setMadeBets($madeBets);
        $statistic->setTotal($total);
        $statistic->setIdent($activeCombination->getIdent());
        $statistic->setBetRowNames($betRowNames);

        return $statistic;
    }

    /**
     * @param CombinationMadeBetEvent[] $events
     * @param SpmFixture $fixture
     * @param PlacedBet $bet
     * @param bool $includeDoubledPlacements
     * @return void
     */
    private function addEvent(array &$events, SpmFixture $fixture, PlacedBet $bet, bool $includeDoubledPlacements = true): void
    {
        if (array_key_exists($fixture->getApiId(), $events)) {
            if (!$includeDoubledPlacements) {
                return;
            }
            $events[$fixture->getApiId()]->setOccurrence($events[$fixture->getApiId()]->getOccurrence() + 1);
            return;
        }

        $combination = new CombinationMadeBetEvent();
        $combination->setOccurrence(1);
        $combination->setOdd($bet->getOdd());
        $combination->setOutcome($bet->getOutput() - $bet->getWager());
        $combination->setEventDate($fixture->getStartingAt());
//        $combination->setShortName($bet->getBetRow()->getBetRowFilters()->toArray()[0]);
        $events[$fixture->getApiId()] = $combination;

    }
}
