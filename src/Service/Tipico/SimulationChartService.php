<?php
declare(strict_types=1);

namespace App\Service\Tipico;

use App\Entity\BettingProvider\Simulator;
use App\Entity\BettingProvider\TipicoPlacement;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;


class SimulationChartService
{
    public function __construct(
        private readonly ChartBuilderInterface $chartBuilder
    )
    {
    }

    public function getCashBoxChart(Simulator $simulator, array $cashBoxValues, bool $timeBased = true): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);

        $placements = $simulator->getTipicoPlacements()->toArray();
        $nr = array_keys($placements);
        $nr[] = count($nr);

        if ($timeBased) {
            if(count($placements)){
                $created = $placements[array_key_first($placements)]->getCreated();
                $nr = [$created->format('d/m H:i (l)')];
                foreach ($placements as $placement) {
                    $created = $placement->getCreated();
                    $nr[] = $created->format('d/m H:i (l)');
                }
            }
        }

        $chart->setData([
            'labels' => $nr,
            'datasets' => [
                [
                    'pointRadius' => 1.5,
                    'label' => 'CashBox',
                    'backgroundColor' => 'rgb(71, 80, 62)',
                    'borderColor' => 'rgb(71, 80, 62)',
                    'Color' => '#ffffff',
                    'data' => $cashBoxValues,
                ],
                [
                    'pointRadius' => 0.0,
                    'label' => 'Start',
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
                    'ticks' => [
                        'color' => '#ffffff'
                    ]
                ],
                'x' => [
                    'ticks' => [
                        'color' => '#ffffff'
                    ]
                ],
            ],
        ]);
        return $chart;
    }

    public function getDailyDistributionChart(array $weekDayStatistics): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);

        $labels = array_keys($weekDayStatistics);

        $wins = [];
        $looses = [];
        foreach ($weekDayStatistics as $value){
            $wins[] = $value['won'];
            $looses[] = $value['loose'];
        }

        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Won',
                    'backgroundColor' => 'rgb(71, 80, 62)',
                    'borderColor' => 'rgb(71, 80, 62)',
                    'data' => $wins,
                ],
                [
                    'label' => 'Loose',
                    'backgroundColor' => 'rgb(198, 0, 15)',
                    'borderColor' => 'rgb(198, 0, 15)',
                    'data' => $looses,
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 20,
                ],
            ],
        ]);
        return $chart;
    }

    public function getValueToWinDistributionChart(array $valueToWinStatistics): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);

        $labels = array_keys($valueToWinStatistics);

        $wonPercentages = [];
        $distributions = [];
        foreach ($valueToWinStatistics as $value){
            $wonPercentages[] = $value[SimulationStatisticService::KEY_WON_PERCENTAGE];
            $distributions[] = $value[SimulationStatisticService::KEY_DISTRIBUTION_PERCENTAGE];
        }

        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Win rate',
                    'backgroundColor' => 'rgb(71, 80, 62)',
                    'borderColor' => 'rgb(71, 80, 62)',
                    'data' => $wonPercentages,
                ],
                [
                    'label' => 'Occurrence',
                    'backgroundColor' => 'rgb(198, 0, 15)',
                    'borderColor' => 'rgb(198, 0, 15)',
                    'data' => $distributions,
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 50,
                ],
            ],
        ]);
        return $chart;
    }

    public function getBetOutcomeChart(Simulator $simulator): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);

        $placements = $simulator->getTipicoPlacements()->toArray();
        $nr = array_keys($placements);

        $betOutcomes = array_map(
            function (TipicoPlacement $placement) {
                $value = 0.0 - $placement->getInput();
                if ($placement->isWon()) {
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
                    'pointRadius' => 1.5,
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
                    'suggestedMax' => 8,
                ],
            ],
        ]);
        return $chart;
    }

    public function getDailyEventChart(array $distribution): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);

        $labels = array_keys($distribution);

        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Occurrence',
                    'backgroundColor' => '#A28E04D1',
                    'borderColor' => '#A28E04D1',
                    'data' => $distribution,
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 5,
                    'ticks' => [
                        'color' => '#ffffff'
                    ]
                ],
                'x' => [
                    'ticks' => [
                        'color' => '#ffffff'
                    ]
                ]
            ],
        ]);
        return $chart;
    }

    public function getBalanceColoredChart(array $distribution): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);

        $labels = array_keys($distribution);

        // Array to hold color values
        $colors = [];
        foreach ($distribution as $value) {
            if ($value > 0) {
                $colors[] = '#0b2e13'; // Green for positive values
            } else {
                $colors[] = '#600c16'; // Red for negative values
            }
        }

        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Occurrence',
                    'backgroundColor' => $colors,
                    'borderColor' => $colors,
                    'data' => $distribution,
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 5,
                    'ticks' => [
                        'color' => '#ffffff'
                    ]
                ],
                'x' => [
                    'ticks' => [
                        'color' => '#ffffff'
                    ]
                ]
            ],
        ]);
        return $chart;
    }

    public function getSimulatorCashBoxDistributionChart(array $distribution)
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);

        $labels = array_keys($distribution);

        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Occurrence',
                    'backgroundColor' => '#A28E04D1',
                    'borderColor' => '#A28E04D1',
                    'data' => $distribution,
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 5,
                    'ticks' => [
                        'color' => '#ffffff'
                    ]
                ],
                'x' => [
                    'ticks' => [
                        'color' => '#ffffff'
                    ]
                ]
            ],
        ]);
        return $chart;
    }

    /**
     * @param TipicoPlacement[] $placements
     */
    public function getWeekdayChart(array $placements): Chart
    {
        $nr = array_keys($placements);
        $nr[] = count($nr);

        $cashBoxValues = [];

        $value = 0.0;
        if(count($placements)){
            $created = $placements[array_key_first($placements)]->getCreated();
            $nr = [$created->format('d/m H:i (l)')];
            foreach ($placements as $placement) {
                $created = $placement->getCreated();
                $nr[] = $created->format('d/m H:i (l)');
                $cashBoxValues[] = $value;
                $value = $value - $placement->getInput();
                if ($placement->isWon()){
                    $value = $value + $placement->getValue() ;
                }
            }
        }


        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);

        $chart->setData([
            'labels' => $nr,
            'datasets' => [
                [
                    'pointRadius' => 1.5,
                    'label' => 'CashBox',
                    'backgroundColor' => 'rgb(71, 80, 62)',
                    'borderColor' => 'rgb(71, 80, 62)',
                    'Color' => '#ffffff',
                    'data' => $cashBoxValues,
                ],
                [
                    'pointRadius' => 0.0,
                    'label' => 'Start',
                    'backgroundColor' => 'rgb(198, 0, 15)',
                    'borderColor' => 'rgb(198, 0, 15)',
                    'data' => array_fill(0, count($nr), 0.0),
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0.0,
                    'suggestedMax' => 10.0,
                    'ticks' => [
                        'color' => '#ffffff'
                    ]
                ],
                'x' => [
                    'ticks' => [
                        'color' => '#ffffff'
                    ]
                ],
            ],
        ]);
        return $chart;
    }
}
