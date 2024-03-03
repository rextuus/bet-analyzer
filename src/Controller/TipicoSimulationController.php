<?php

namespace App\Controller;

use App\Entity\Simulator;
use App\Entity\TipicoPlacement;
use App\Service\Statistic\BetRowCombinationStatistic;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/tipico/simulation')]
class TipicoSimulationController extends AbstractController
{
    public function __construct(
        private readonly SimulatorService $simulatorService,
        private readonly ChartBuilderInterface $chartBuilder
    )
    {
    }

    #[Route('/', name: 'app_tipico_simulation_dashboard')]
    public function index(): Response
    {
        $simulators = $this->simulatorService->findAll();

        $betOutcomeCharts = [];
        foreach ($simulators as $simulator){
            $betOutcomeCharts[] = $this->createBetOutcomeChart($simulator);
        }

        $cashBoxCharts = [];
        foreach ($simulators as $simulator){
            $cashBoxCharts[] = $this->createCashBoxChart($simulator);
        }

        return $this->render('tipico_simulation/dashboard.html.twig', [
            'simulators' => $simulators,
            'betOutcomeCharts' => $betOutcomeCharts,
            'cashBoxCharts' => $cashBoxCharts,
        ]);
    }

    #[Route('/{simulator}/placements', name: 'app_tipico_simulation_placements')]
    public function placements(Simulator $simulator): Response
    {
        $placements = $simulator->getTipicoPlacements();

        return $this->render('tipico_simulation/placements.html.twig', [
            'placements' => $placements,
            'simulator' => $simulator,
        ]);
    }

    public function createBetOutcomeChart(Simulator $simulator): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);

        $placements = $simulator->getTipicoPlacements()->toArray();
        $nr = array_keys($placements);

        $betOutcomes = array_map(
            function (TipicoPlacement $placement){
                $value = 0.0 - $placement->getInput();
                if ($placement->isWon()){
                    $value = ($placement->getValue() * $placement->getInput()) - $placement->getInput();
                }
                return $value;
            },
            $placements
        );

        $chart->setData([
            'labels' => $nr,
            'datasets' => [
                [
//                    'pointRadius' => 0.5,
                    'label' => '',
                    'backgroundColor' => 'rgb(71, 80, 62)',
                    'borderColor' => 'rgb(71, 80, 62)',
                    'data' => $betOutcomes,
                ],
                [
                    'pointRadius' => 0.0,
                    'label' => '',
                    'backgroundColor' => 'rgb(198, 0, 15)',
                    'borderColor' => 'rgb(198, 0, 15)',
                    'data' => array_fill(0, count($nr), 0.0),
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => -2,
                    'suggestedMax' => 10,
                ],
            ],
        ]);
        return $chart;
    }

    public function createCashBoxChart(Simulator $simulator): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);

        $placements = $simulator->getTipicoPlacements()->toArray();
        $nr = array_keys($placements);

        $betOutcomes = array_map(
            function (TipicoPlacement $placement){
                $value = 0.0 - $placement->getInput();
                if ($placement->isWon()){
                    $value = ($placement->getValue() * $placement->getInput()) - $placement->getInput();
                }
                return $value;
            },
            $placements
        );

        $cashBoxValues = [0 => 100.0];
        foreach ($betOutcomes as $nrr => $betOutcome){
            $cashBoxValues[$nrr+1] = $cashBoxValues[$nrr] + $betOutcome;
        }

        $chart->setData([
            'labels' => $nr,
            'datasets' => [
                [
//                    'pointRadius' => 0.5,
                    'label' => '',
                    'backgroundColor' => 'rgb(71, 80, 62)',
                    'borderColor' => 'rgb(71, 80, 62)',
                    'data' => $cashBoxValues,
                ],
                [
                    'pointRadius' => 0.0,
                    'label' => '',
                    'backgroundColor' => 'rgb(198, 0, 15)',
                    'borderColor' => 'rgb(198, 0, 15)',
                    'data' => array_fill(0, count($nr), 100.0),
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 120.0,
                    'suggestedMax' => 80.0,
                ],
            ],
        ]);
        return $chart;
    }
}
