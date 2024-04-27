<?php
declare(strict_types=1);

namespace App\Service\Statistic;

use App\Entity\Spm\BetRowSummary;
use App\Entity\Spm\SpmLeague;
use App\Entity\Spm\SpmSeason;
use App\Service\Evaluation\Content\BetRow\SimpleBetRow\SimpleBetRowRepository;
use App\Service\Sportmonks\Content\Season\SpmSeasonService;
use App\Service\Statistic\Content\BetRowCombination\BetRowCombinationService;
use App\Service\Statistic\Dto\League\Row;
use App\Service\Statistic\Dto\League\RowInformation;
use Exception;


class LeagueStatisticService
{


    public function __construct(private SpmSeasonService $seasonService, private BetRowCombinationService $betRowCombinationService, private SimpleBetRowRepository $betRowRepository)
    {
    }

    /**
     * @return Row[]
     * @throws Exception
     */
    public function calculateDistribution(SpmLeague $league, float $min): array
    {
        $information = new RowInformation();

        $rows = $this->betRowRepository->findRowsExistingInAllSeasonsOfLeagueInDeltaRange($league, $min, 200.0);
        $information->setDescription($league->getName());

        $map = new SeasonBetRowMap();
        $currentKey = -1;
        foreach ($rows as $row){
            if ($row instanceof SpmSeason){
                $map->addSeason($row->getApiId());
                $currentKey = $row->getApiId();
            }
            if ($row instanceof BetRowSummary){
                $map->addBetRow($currentKey, $row);
            }
        }

        $distribution = new SeasonBetRowDistribution();
        // goal: variant = [season1, season3, ...]

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

    /**
     * @return Row[]
     * @throws Exception
     */
    private function createRows(SeasonBetRowDistribution $betRowDistribution): array
    {
        $activeCombination = $this->betRowCombinationService->getActiveCombination();
        $alreadyAddedRows = $activeCombination->getBetRows()->toArray();

        $rows = [];
        foreach ($betRowDistribution->getVariantsDescending() as $variant => $rowSummaries) {
            $row = new Row();

            $displayNames = [];
            $ids = [];
            $seasons = [];
            $alreadyAddedRowsCounter = 0;
            foreach ($rowSummaries as $seasonApi => $summary) {
                $season = $this->seasonService->findBy(['apiId' => $summary->getBetRow()->getSeasonApiId()])[0];
                $displayNames[$seasonApi] = $season->getDisplayName() . ' ' . $summary->getDisplayName() . ': ' . round($summary->getCashBox(), 2);
                $seasons[$seasonApi] = $season;
                $ids[] = $summary->getBetRow()->getId();
                if (in_array($summary->getBetRow(), $alreadyAddedRows)){
                    $alreadyAddedRowsCounter++;
                }
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
            $row->setRowIds(implode('~', $ids));
            $row->setTotalAmount(array_sum($cashBoxes));

            $row->setAddable(count($rowSummaries) !== $alreadyAddedRowsCounter);
            if ($row->isAddable()){
                $row->setButtonClass('active');
            }

            $rows[] = $row;
        }
        return $rows;
    }
}
