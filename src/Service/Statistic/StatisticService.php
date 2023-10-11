<?php
declare(strict_types=1);

namespace App\Service\Statistic;

use App\Entity\BetRowOddFilter;
use App\Entity\SimpleBetRow;
use App\Entity\SpmSeason;
use App\Service\Evaluation\BetOn;
use App\Service\Evaluation\Content\BetRow\SimpleBetRow\SimpleBetRowService;
use App\Service\Evaluation\SlideWindowFactory;
use App\Service\Sportmonks\Content\Season\SpmSeasonService;
use App\Service\Statistic\Content\BetRowSummary\BetRowSummaryService;
use App\Service\Statistic\Content\BetRowSummary\Data\BetRowSummaryData;
use App\Service\Statistic\Content\SeasonSummary\Data\SeasonSummaryData;
use App\Service\Statistic\Content\SeasonSummary\SeasonSummaryService;
use App\Service\Statistic\Dto\BetRowStatistics;
use App\Service\Statistic\Dto\SeasonDto;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class StatisticService
{


    public function __construct(
        private readonly SimpleBetRowService  $betRowService,
        private readonly SpmSeasonService     $seasonService,
        private readonly SlideWindowFactory   $slideWindowFactory,
        private readonly SeasonSummaryService $seasonSummaryService,
        private readonly BetRowSummaryService $betRowSummaryService,
    )
    {
    }

    public function getSeasonDtos(): array
    {
        $seasonsWithBets = $this->seasonService->findApprovedSeasonsBetRows(true);

        $dtos = [];
        foreach ($seasonsWithBets as $seasonsWithBet) {
            $dtos[] = $this->getSeasonDto($seasonsWithBet[0]);
        }

        return $dtos;
    }

    public function getSeasonDto(SpmSeason $season): void
    {
        $data = new SeasonSummaryData();
        $data->setSeason($season);

        $betRowsOfSeason = $this->betRowService->findBy(['seasonApiId' => $season->getApiId()]);
        $this->createBetRowsStatistic($betRowsOfSeason, $data);
        $this->storeSeasonSummary($betRowsOfSeason, $data);
    }

    /**
     * @param SimpleBetRow[] $betRows
     * @param SeasonSummaryData $data
     * @return void
     */
    private function createBetRowsStatistic(array $betRows, SeasonSummaryData $data): void
    {
        $slideWindow = $this->slideWindowFactory->calculateStepsForSlideWindow(1.0, 5.0, 0.1);
        $decreaseWindow = $this->slideWindowFactory->calculateStepsForDecreasingWindow(1.0, 5.0, 0.1);
        $betRowCreationMap = [
            BetOn::HOME->value => array_merge($this->slideWindowFactory->convertWindowToMap($slideWindow), $this->slideWindowFactory->convertWindowToMap($decreaseWindow)),
            BetOn::DRAW->value => array_merge($this->slideWindowFactory->convertWindowToMap($slideWindow), $this->slideWindowFactory->convertWindowToMap($decreaseWindow)),
            BetOn::AWAY->value => array_merge($this->slideWindowFactory->convertWindowToMap($slideWindow), $this->slideWindowFactory->convertWindowToMap($decreaseWindow))
        ];

        foreach ($betRows as $betRow) {
            /** @var BetRowOddFilter[] $filters */
            $filters = $betRow->getBetRowFilters()->toArray();
            foreach ($filters as $filter) {
                $betRowCreationMap[$filter->getBetOn()->value][$filter->getMin() . '-' . $filter->getMax()] = true;
            }
        }

        $mappingFunction = function ($key, $rowPresent) {
            if (!$rowPresent) {
                return $key;
            }
            return null;
        };
        $filterFunction = function ($rowPresent) {
            return !is_null($rowPresent);
        };

        $missingHomes = array_filter(
            array_map(
                $mappingFunction,
                array_keys($betRowCreationMap[BetOn::HOME->value]),
                array_values($betRowCreationMap[BetOn::HOME->value])
            ),
            $filterFunction
        );
        $data->setMissingHomeFilters($missingHomes);

        $missingDraws = array_filter(
            array_map(
                $mappingFunction,
                array_keys($betRowCreationMap[BetOn::DRAW->value]),
                array_values($betRowCreationMap[BetOn::DRAW->value])
            ),
            $filterFunction
        );
        $data->setMissingDrawFilters($missingDraws);

        $missingAway = array_filter(
            array_map(
                $mappingFunction,
                array_keys($betRowCreationMap[BetOn::AWAY->value]),
                array_values($betRowCreationMap[BetOn::AWAY->value])
            ),
            $filterFunction
        );
        $data->setMissingAwayFilters($missingAway);
    }

    /**
     * @param SimpleBetRow[] $betRows
     * @param SeasonSummaryData $data
     * @return void
     */
    private function storeSeasonSummary(array $betRows, SeasonSummaryData $data): void
    {
        if (!count($betRows)) {
            $data->setHighest(null);
            $data->setMissingHomeFilters([]);
            $data->setMissingDrawFilters([]);
            $data->setMissingAwayFilters([]);

            return;
        }

        $highest = 0.0;
        $highestCashBoxNr = 0;

        $rowSummaryDatas = [];
        foreach ($betRows as $nr => $betRow) {
            $betRowData = new BetRowSummaryData();

            $cashBox = $betRow->getCashBox();
            $wager = $betRow->getWager();
            $daysMadeBets = array_fill(0, 50, 0);
            $daysMadeOutcomes = array_fill(0, 50, 0.0);
            foreach ($betRow->getPlacedBets() as $bet) {
                $win = 0.0;
                if ($bet->isWon()) {
                    $win = $bet->getOdd() * $wager - ($bet->getOdd() * $wager * 0.05);
                }
                $cashBox = $cashBox - $wager + $win;

                $daysMadeBets[$bet->getMatchDay()] = $daysMadeBets[$bet->getMatchDay()] + 1;
                $daysMadeOutcomes[$bet->getMatchDay()] = $daysMadeOutcomes[$bet->getMatchDay()] + $win - $wager;
            }

            if ($cashBox > $highest) {
                $highestCashBoxNr = $nr;
                $highest = $cashBox;
            }

            //set betRow data
            $betRowData->setBetRow($betRow);
            $betRowData->setMadeBets(count($betRow->getPlacedBets()));
            $betRowData->setCashBox($cashBox);

            $betRowData->setDaysMadeBets($daysMadeBets);
            $betRowData->setDaysOutcomes($daysMadeOutcomes);
            $betRowData->setDailyReproductionChance(count($betRow->getPlacedBets()));

            // set defaults
            $betRowData->setLowest(0.0);
            $betRowData->setHighest(0.0);
            $betRowData->setSeriesStatistics([]);
            $betRowData->setPositiveDays(0);
            $this->checkDailyReproduction($betRowData, $daysMadeOutcomes);

            $rowSummaryDatas[] = $betRowData;
        }

        $data->setHighest($betRows[$highestCashBoxNr]);

        $this->betRowSummaryService->createMultipleByData($rowSummaryDatas);
        if (!$this->seasonSummaryService->findBy(['season' => $data->getSeason()->getId()])) {
            $this->seasonSummaryService->createByData($data);
        }
    }

    private function checkDailyReproduction(BetRowSummaryData $betRowData, array $daysMadeOutcomes): bool
    {
        // reduce to non 0.0 values
        $daysMadeOutcomes = array_filter(
            $daysMadeOutcomes,
            function ($value) {
                if ($value !== 0.0) {
                    return true;
                }
                return false;
            }
        );

        if (count($daysMadeOutcomes) === 0) {
            return false;
        }

        $minStart = array_keys($daysMadeOutcomes)[0];
        $maxStart = array_keys($daysMadeOutcomes)[0];
        $lastDay = 0;
        $lastOutcome = 0.0;

        // collect all series
        $seriesCounter = 0;
        $series = [];
        foreach ($daysMadeOutcomes as $day => $outcome) {
            // start positive series
            if ($outcome > 0 && $lastOutcome < 0.0) {
                $maxStart = $day;
            }
            // end positive series
            if ($outcome < 0.0 && $lastOutcome > 0.0) {
                $series[] = new SeriesStatistic(SeriesVariant::POSITIVE, $maxStart, $lastDay, $seriesCounter);
                $seriesCounter = 0;
            }

            // start negative series
            if ($outcome < 0 && $lastOutcome > 0.0) {
                $minStart = $day;
            }
            // end negative series
            if ($outcome > 0.0 && $lastOutcome < 0.0) {
                $series[] = new SeriesStatistic(SeriesVariant::NEGATIVE, $minStart, $lastDay, $seriesCounter);
                $seriesCounter = 0;
            }

            // special last entry
            if ($day === array_key_last($daysMadeOutcomes)) {
                if ($outcome > 0.0) {
                    $series[] = new SeriesStatistic(SeriesVariant::POSITIVE, $maxStart, $day, $seriesCounter);
                }
                if ($outcome < 0.0) {
                    $series[] = new SeriesStatistic(SeriesVariant::NEGATIVE, $minStart, $day, $seriesCounter);
                }
            }

            $lastOutcome = $outcome;
            $lastDay = $day;
            $seriesCounter++;
        }


        // collect highest and lowes
        sort($daysMadeOutcomes);
        $betRowData->setLowest($daysMadeOutcomes[0]);
        $betRowData->setHighest($daysMadeOutcomes[array_key_last($daysMadeOutcomes)]);

        $positiveDays = 0;
        $dayWithBets = 0;
        $positiveNonSingles = 0;
        $seriesRaw = [];
        foreach ($series as $seriesEntry) {
            if ($seriesEntry->getVariant() === SeriesVariant::POSITIVE) {
                $positiveDays = $positiveDays + $seriesEntry->getDays();
                if ($seriesEntry->getDays() !== 1) {
                    $positiveNonSingles = $positiveNonSingles + $seriesEntry->getDays();
                }
            }
            $dayWithBets = $dayWithBets + $seriesEntry->getDays();

            $seriesRaw[] = [$seriesEntry->getStart(), $seriesEntry->getEnd(), $seriesEntry->getDays(), $seriesEntry->getVariant()];
        }
        $betRowData->setSeriesStatistics($seriesRaw);

        $betRowData->setPositiveDays(0.0);
        if ($dayWithBets > 0) {
            $betRowData->setPositiveDays($positiveDays / $dayWithBets);
        }

        $betRowData->setDailyReproductionChance(0.0);
        if ($positiveDays > 0) {
            $betRowData->setDailyReproductionChance($positiveNonSingles / $positiveDays);
        }

        return true;
    }
}
