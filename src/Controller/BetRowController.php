<?php

namespace App\Controller;

use App\Entity\BetRowSummary;
use App\Entity\SimpleBetRow;
use App\Entity\SpmLeague;
use App\Entity\SpmSeason;
use App\Service\Evaluation\Content\BetRow\SimpleBetRow\SimpleBetRowRepository;
use App\Service\Evaluation\Content\BetRow\SimpleBetRow\SimpleBetRowService;
use App\Service\Statistic\Dto\BetRowDto;
use App\Service\Statistic\Dto\League\Row;
use App\Service\Statistic\LeagueStatisticService;
use App\Service\Statistic\SeasonBetRowDistribution;
use App\Service\Statistic\SeasonBetRowMap;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/statistic')]
class BetRowController extends AbstractController
{
    public function __construct(private SimpleBetRowService $betRowService, private ChartBuilderInterface $chartBuilder)
    {
    }

    #[Route('/season/{apiId}', name: 'app_bet_row_list')]
    public function index(SpmSeason $season): Response
    {
//        $betRows = $this->betRowService->findBy(['seasonApiId' => 19744]);
        $dtos = [];
        $cut = 34;
        $raw = $this->betRowService->findBySeasonIncludingSummaries($season);
        for ($rowNr = 0; $rowNr < count($raw); $rowNr=$rowNr+2) {
            $dto = new BetRowDto();
            /** @var SimpleBetRow $row */
            $row = $raw[$rowNr];
            /** @var BetRowSummary $rowSummary */
            $rowSummary = $raw[$rowNr+1];

            $madeBetsChart = $this->createMadeBetsChart($rowSummary, $cut);
            $outcomeChart = $this->createOutcomeChart($rowSummary, $cut);

            $dto->setMadeBetsChart($madeBetsChart);
            $dto->setOutcomeChart($outcomeChart);
            $dto->setCashBox($rowSummary->getCashBox());
            $dto->setFilter((string )$row->getBetRowFilters()[0]);
            $dto->setMadeBets($rowSummary->getMadeBets());

            $dtos[] = $dto;
        }


        return $this->render('bet_row/index.html.twig', [
            'season' => $season,
            'dtos' => $dtos,
        ]);
    }

    #[Route('/league/{apiId}', name: 'app_bet_row_league')]
    public function league(SpmLeague $league, SimpleBetRowRepository $betRowRepository, LeagueStatisticService $leagueStatisticService): Response
    {
        $rows = $betRowRepository->findRowsExistingInAllSeasonsOfLeagueInDeltaRange($league, 100.01, 200.0);

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

        $rows = $leagueStatisticService->calculateDistribution($map);
        foreach ($rows as $row){
            $row->setChart($this->createLeagueChart($row->getChartData(), 1));
        }


        return $this->render('bet_row/league.html.twig', [
            'rows' => $rows
        ]);
    }


    public function createLeagueChart(array $data, int $cut): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);

        $chart->setData([
            'labels' => $data['years'],
            'datasets' => [
                [
//                    'pointRadius' => 0.5,
                    'label' => '',
                    'backgroundColor' => 'rgb(71, 80, 62)',
                    'borderColor' => 'rgb(71, 80, 62)',
                    'data' => $data['cashBoxes'],
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 80,
                    'suggestedMax' => 120,
                ],
            ],
        ]);
        return $chart;
    }


    public function createMadeBetsChart(BetRowSummary $rowSummary, int $cut): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);

        $chart->setData([
            'labels' => array_slice(array_keys($rowSummary->getDaysMadeBets()), 0, $cut),
            'datasets' => [
                [
                    'pointRadius' => 0.5,
                    'label' => '',
                    'backgroundColor' => 'rgb(71, 80, 62)',
                    'borderColor' => 'rgb(71, 80, 62)',
                    'data' => array_slice(array_values($rowSummary->getDaysMadeBets()), 0, $cut),
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 10,
                ],
            ],
        ]);
        return $chart;
    }

    /**
     * @param BetRowSummary $rowSummary
     * @return Chart
     */
    public function createOutcomeChart(BetRowSummary $rowSummary, int $cut): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);

        $chart->setData([
            'labels' => array_slice(array_keys($rowSummary->getDaysOutcomes()), 0, $cut),
            'datasets' => [
                [
                    'pointRadius' => 0.5,
                    'label' => '',
                    'backgroundColor' => 'rgb(100, 100, 132)',
                    'borderColor' => 'rgb(100, 100, 132)',
                    'data' => array_slice(array_values($rowSummary->getDaysOutcomes()), 0, $cut),
                ],
                [
                    'pointRadius' => 0.0,
                    'label' => '',
                    'backgroundColor' => 'rgb(198, 0, 15)',
                    'borderColor' => 'rgb(198, 0, 15)',
                    'data' => array_fill(0, $cut, 0.0),
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => -10,
                    'suggestedMax' => 10,
                ],
            ],
        ]);
        return $chart;
    }
}
