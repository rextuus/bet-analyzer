<?php
declare(strict_types=1);

namespace App\Service\Statistic;

use App\Entity\BetRowSummary;
use App\Entity\SpmSeason;
use App\Service\Sportmonks\Content\Season\SpmSeasonService;
use App\Service\Statistic\Dto\League\Row;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class LeagueStatisticService
{


    public function __construct(private SpmSeasonService $seasonService)
    {
    }

    /**
     * @return Row[]
     */
    public function calculateDistribution(SeasonBetRowMap $map): array
    {
        $distribution = new SeasonBetRowDistribution();
        // goal: variant = [season1, season3, ...]
//        dd($map->getMap());

        $result = [];
        $isInAll = true;
        foreach ($map->getMap() as $season => $seasonBetRows) {
            foreach ($seasonBetRows as $variant => $seasonBetRow) {
                $distribution->addVariant($variant, $season, $seasonBetRow);
            }
        }

        // sort rows so that with highest total amount is top
        $rows = $this->createRows($distribution);
        usort($rows,
        function (Row $a, Row $b){
            return array_sum($a->getChartData()['cashBoxes']) < array_sum($b->getChartData()['cashBoxes']);
        }
        );

        return $rows;
    }

    private function getSeasonWithMostBetRows(SeasonBetRowMap $map): array
    {
        $highest = 0;
        $highestNr = 0;
        foreach ($map->getMap() as $nr => $season) {
            if (count($season) > $highest) {
                $highest = count($season);
                $highestNr = $nr;
            }
        }
        return $map->getMap()[$highestNr];
    }


    /**
     * @return Row[]
     */
    private function createRows(SeasonBetRowDistribution $betRowDistribution): array
    {
        $rows = [];
        foreach ($betRowDistribution->getVariantsDescending() as $variant => $rowSummaries) {
            $row = new Row();

            $displayNames = [];
            $seasons = [];
            foreach ($rowSummaries as $seasonApi => $summary) {
                $season = $this->seasonService->findBy(['apiId' => $summary->getBetRow()->getSeasonApiId()])[0];
                $displayNames[$seasonApi] = $season->getDisplayName() . ' ' . $summary->getDisplayName() . ': ' . round($summary->getCashBox(), 2);
                $seasons[$seasonApi] = $season;
            }

            $cashBoxes = array_map(
                function (BetRowSummary $summary) {
                    return round($summary->getCashBox(), 2);
                },
                $rowSummaries
            );

            // chart for timeOrder
            $timeOrder = array_map(
                function (SpmSeason $season) {

                    return $season->getStartingAt();
                },
                $seasons
            );

            arsort($timeOrder, SORT_ASC);
            $timeOrder = array_reverse($timeOrder, true);

            $timeOrderChartData = ['years' => [], 'cashBoxes' => []];
            foreach ($timeOrder as $seasonApiId => $entry){
                $timeOrderChartData['years'][] = $entry->format('Y');
                $timeOrderChartData['cashBoxes'][] = $cashBoxes[$seasonApiId];
            }


            arsort($cashBoxes, SORT_DESC);

            $range = round($cashBoxes[array_key_first($cashBoxes)] - $cashBoxes[array_key_last($cashBoxes)], 2);
            $medianKey = array_keys($cashBoxes)[(int)floor(count($cashBoxes) / 2)];

            $row->setChartData($timeOrderChartData);
            $row->setDisplayNames($displayNames);

//            dump($medianKey);
//            dump($desc);
//            dump($desc[$medianKey]);
//            dump($range);
            $rows[] = $row;
        }
        return $rows;
    }
}
