<?php
declare(strict_types=1);

namespace App\Service\Tipico;

use App\Entity\BettingProvider\Simulator;
use App\Entity\BettingProvider\TipicoPlacement;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;


class SimulationChartService
{
    // Farben wie in assets/styles/const.scss (dunkles Schema)
    private const COLOR_POSITIVE = '#3fb27f';
    private const COLOR_NEGATIVE = '#e5566a';
    private const COLOR_ACCENT = '#e0b33a';
    private const COLOR_MUTED = '#93a0b8';

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
                    'backgroundColor' => self::COLOR_POSITIVE,
                    'borderColor' => self::COLOR_POSITIVE,
                    'Color' => '#ffffff',
                    'data' => $cashBoxValues,
                ],
                [
                    'pointRadius' => 0.0,
                    'label' => 'Start',
                    'backgroundColor' => self::COLOR_NEGATIVE,
                    'borderColor' => self::COLOR_NEGATIVE,
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
                        'color' => self::COLOR_MUTED
                    ]
                ],
                'x' => [
                    'ticks' => [
                        'color' => self::COLOR_MUTED
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
                    'backgroundColor' => self::COLOR_POSITIVE,
                    'borderColor' => self::COLOR_POSITIVE,
                    'data' => $wins,
                ],
                [
                    'label' => 'Loose',
                    'backgroundColor' => self::COLOR_NEGATIVE,
                    'borderColor' => self::COLOR_NEGATIVE,
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

    public function getDailyDistributionChartLine(array $weekDayStatistics): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);

        $labels = array_keys($weekDayStatistics);

        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Won',
                    'backgroundColor' => self::COLOR_POSITIVE,
                    'borderColor' => self::COLOR_POSITIVE,
                    'data' => $weekDayStatistics,
                ],
                [
                    'pointRadius' => 0.0,
                    'label' => 'Start',
                    'backgroundColor' => self::COLOR_NEGATIVE,
                    'borderColor' => self::COLOR_NEGATIVE,
                    'data' => array_fill(0, count($labels), 0.0),
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
                    'backgroundColor' => self::COLOR_POSITIVE,
                    'borderColor' => self::COLOR_POSITIVE,
                    'data' => $wonPercentages,
                ],
                [
                    'label' => 'Occurrence',
                    'backgroundColor' => self::COLOR_NEGATIVE,
                    'borderColor' => self::COLOR_NEGATIVE,
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
                    'backgroundColor' => self::COLOR_POSITIVE,
                    'borderColor' => self::COLOR_POSITIVE,
                    'data' => $betOutcomes,
                ],
                [
                    'pointRadius' => 0.0,
                    'label' => '',
                    'backgroundColor' => self::COLOR_NEGATIVE,
                    'borderColor' => self::COLOR_NEGATIVE,
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
                    'backgroundColor' => self::COLOR_ACCENT,
                    'borderColor' => self::COLOR_ACCENT,
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
                        'color' => self::COLOR_MUTED
                    ]
                ],
                'x' => [
                    'ticks' => [
                        'color' => self::COLOR_MUTED
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
            if ($value > 0.0) {
                $colors[] = self::COLOR_POSITIVE; // Green for positive values
            } else {
                $colors[] = self::COLOR_NEGATIVE; // Red for negative values
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
                        'color' => self::COLOR_MUTED
                    ]
                ],
                'x' => [
                    'ticks' => [
                        'color' => self::COLOR_MUTED
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
                    'backgroundColor' => self::COLOR_ACCENT,
                    'borderColor' => self::COLOR_ACCENT,
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
                        'color' => self::COLOR_MUTED
                    ]
                ],
                'x' => [
                    'ticks' => [
                        'color' => self::COLOR_MUTED
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
                    'backgroundColor' => self::COLOR_POSITIVE,
                    'borderColor' => self::COLOR_POSITIVE,
                    'Color' => '#ffffff',
                    'data' => $cashBoxValues,
                ],
                [
                    'pointRadius' => 0.0,
                    'label' => 'Start',
                    'backgroundColor' => self::COLOR_NEGATIVE,
                    'borderColor' => self::COLOR_NEGATIVE,
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
                        'color' => self::COLOR_MUTED
                    ]
                ],
                'x' => [
                    'ticks' => [
                        'color' => self::COLOR_MUTED
                    ]
                ],
            ],
        ]);
        return $chart;
    }
}
