<?php

namespace App\Controller;

use App\Entity\BetRowSummary;
use App\Entity\SimpleBetRow;
use App\Entity\SpmSeason;
use App\Form\BetRowCombinationChoiceData;
use App\Form\BetRowCombinationChoiceType;
use App\Form\BetRowCombinationCreateData;
use App\Form\BetRowCombinationCreateType;
use App\Form\LeagueStatisticFilterData;
use App\Form\SeasonStatisticFilterType;
use App\Service\Evaluation\Content\BetRow\SimpleBetRow\SimpleBetRowRepository;
use App\Service\Evaluation\Content\BetRow\SimpleBetRow\SimpleBetRowService;
use App\Service\Sportmonks\Content\League\SpmLeagueService;
use App\Service\Sportmonks\Content\Season\SpmSeasonService;
use App\Service\Statistic\BetRowCombinationStatistic;
use App\Service\Statistic\BetRowCombinationStatisticService;
use App\Service\Statistic\Content\BetRowCombination\BetRowCombinationService;
use App\Service\Statistic\Content\BetRowCombination\Data\BetRowCombinationData;
use App\Service\Statistic\Dto\BetRowDto;
use App\Service\Statistic\LeagueStatisticService;
use App\Service\Statistic\SeasonBetRowMap;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/statistic')]
class BetRowController extends AbstractController
{
    public function __construct(private SimpleBetRowService $betRowService, private ChartBuilderInterface $chartBuilder, private BetRowCombinationService $betRowCombinationService)
    {
    }

    #[Route('/league/{apiId}', name: 'app_bet_row_list')]
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
            'league' => $season,
            'dtos' => $dtos,
        ]);
    }

    #[Route('/league', name: 'app_bet_row_league')]
    public function league(Request $request, SimpleBetRowRepository $betRowRepository, LeagueStatisticService $leagueStatisticService): Response
    {
        $rows = [];

        $data = new LeagueStatisticFilterData();
        $form = $this->createForm(SeasonStatisticFilterType::class, $data);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            /** @var LeagueStatisticFilterData $data */
            $data = $form->getData();

            $rows = $betRowRepository->findRowsExistingInAllSeasonsOfLeagueInDeltaRange($data->getLeague(), $data->getMin(), 200.0);

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
        }

        return $this->render('bet_row/league.html.twig', [
            'rows' => $rows,
            'form' => $form
        ]);
    }

    #[Route('/combination', name: 'app_bet_row_combination')]
    public function combination(Request $request, SpmSeasonService $seasonService): Response
    {
        $activeOnes = $this->betRowCombinationService->getActiveCombination();
        $selected = $activeOnes->getBetRows()->toArray();
//dd($selected);
        $data = new BetRowCombinationCreateData();
        $form = $this->createForm(BetRowCombinationCreateType::class, $data);

        $data2 = new BetRowCombinationChoiceData();
        $form2 = $this->createForm(BetRowCombinationChoiceType::class, $data2);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            /** @var BetRowCombinationCreateData $data */
            $data = $form->getData();

            $createData = new BetRowCombinationData();
            $createData->setIdent($data->getIdent());
            $createData->setActive($data->isActive());
            $createData->setEvaluated(false);
            $createData->setRows([]);

            if ($data->isActive()){
                $this->deactivateAll();
            }

            $this->betRowCombinationService->createByData($createData);
        }

        $form2->handleRequest($request);
        if ($form2->isSubmitted() && $form2->isValid()){
            /** @var BetRowCombinationChoiceData $data */
            $data = $form2->getData();

            $this->deactivateAll();

            $combination = $data->getCombination();
            $updateData = (new BetRowCombinationData())->initFromEntity($combination);
            $updateData->setActive(true);

            $this->betRowCombinationService->update($combination, $updateData);
            $selected = $combination->getBetRows()->toArray();
        }

        $betRowNames = [];
        foreach ($selected as $selectedOne){
            $season = $seasonService->findBy(['apiId' => $selectedOne->getSeasonApiId()])[0];
            $name = sprintf(
                "%s %s",
                $season->getDisplayName(),
                (string) $selectedOne->getBetRowFilters()[0]
            );
            $betRowNames[] = $name;
        }


        return $this->render('bet_row/combination.html.twig', [
            'form' => $form,
            'form2' => $form2,
            'selected' => $betRowNames
        ]);
    }

    #[Route("/add/{betRows}", name: 'app_bet_row_combination_add')]
    public function check(string $betRows): Response
    {
        $betRowIds = explode('~', $betRows);

        $betRowsToAdd = [];
        foreach ($betRowIds as $betRowId){
            $betRowsToAdd[] = $this->betRowService->findById($betRowId);
        }


        $combination = $this->betRowCombinationService->getActiveCombination();

        $alreadyAddedBetRows = $combination->getBetRows()->toArray();
        $combined = array_unique(array_merge($betRowsToAdd,$alreadyAddedBetRows), SORT_REGULAR);
//dd($combined);
        $combinationData = (new BetRowCombinationData())->initFromEntity($combination);
        $combinationData->setRows($combined);
        $this->betRowCombinationService->update($combination, $combinationData);

        return new Response();
    }

    private function deactivateAll(): void
    {
        $activeOnes = $this->betRowCombinationService->findBy(['active' => true]);
        foreach ($activeOnes as $activeOne){
            $updateData = (new BetRowCombinationData())->initFromEntity($activeOne);
            $updateData->setActive(false);
            $this->betRowCombinationService->update($activeOne, $updateData);
        }
    }

    #[Route("/evaluate", name: 'app_bet_row_combination_evaluate')]
    public function evaluate(BetRowCombinationStatisticService $statisticService): Response
    {
        $statistic = $statisticService->getBetRowCombinationStatistic();
dump($statistic);
        return $this->render('bet_row/result.html.twig', [
            'statistic' => $statistic,
            'chart' => $this->createDailyChart($statistic)
        ]);
    }

    public function createDailyChart(BetRowCombinationStatistic $statistic): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);

        $dates = array_map(
          function (\DateTimeInterface $date){
              return $date->format('d-m-Y');
          },
            $statistic->getChartDataDays()
        );

        $chart->setData([
            'labels' => $dates,
            'datasets' => [
                [
//                    'pointRadius' => 0.5,
                    'label' => '',
                    'backgroundColor' => 'rgb(71, 80, 62)',
                    'borderColor' => 'rgb(71, 80, 62)',
                    'data' => $statistic->getChartDataOutcomes(),
                ],
                [
                    'pointRadius' => 0.0,
                    'label' => '',
                    'backgroundColor' => 'rgb(198, 0, 15)',
                    'borderColor' => 'rgb(198, 0, 15)',
                    'data' => array_fill(0, count($statistic->getChartDataOutcomes()), 0.0),
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
                [
                    'pointRadius' => 0.0,
                    'label' => '',
                    'backgroundColor' => 'rgb(198, 0, 15)',
                    'borderColor' => 'rgb(198, 0, 15)',
                    'data' => array_fill(0, count($data['cashBoxes']), 100.0),
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
