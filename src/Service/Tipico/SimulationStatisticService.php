<?php
declare(strict_types=1);

namespace App\Service\Tipico;

use App\Entity\Simulator;
use App\Entity\TipicoPlacement;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class SimulationStatisticService
{
    public function __construct(
        private readonly ChartBuilderInterface $chartBuilder
    )
    {
    }

    public function getCashBoxChart(Simulator $simulator, bool $timeBased = true): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);

        $placements = $simulator->getTipicoPlacements()->toArray();
        $nr = array_keys($placements);
        $nr[] = count($nr);

        if ($timeBased) {
            $created = $placements[array_key_first($placements)]->getCreated();
            $nr = [$created->format('d/m H:i (l)')];
            foreach ($placements as $placement) {
                $created = $placement->getCreated();
                $nr[] = $created->format('d/m H:i (l)');
            }
        }

        $cashBoxValues = $this->getCashBoxChangeArray($placements);

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

    /**
     * @param TipicoPlacement[] $placements
     * @return float[]
     */
    public function getCashBoxChangeArray(array $placements): array
    {
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

        $cashBoxValues = [0 => 100.0];
        foreach ($betOutcomes as $nrr => $betOutcome) {
            $cashBoxValues[$nrr + 1] = $cashBoxValues[$nrr] + $betOutcome;
        }

        return $cashBoxValues;
    }

    public function getDailyDistributionChart(Simulator $simulator): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);

        $values = $this->getWeekDayStatistics($simulator);
        $labels = array_keys($values);

        $wins = [];
        $looses = [];
        foreach ($values as $value){
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

    private function getWeekDayStatistics(Simulator $simulator): array
    {
        $placements = $simulator->getTipicoPlacements()->toArray();
        $cashBoxChanges = $this->getCashBoxChangeArray($placements);

        $weekDays = array_map(
            function (TipicoPlacement $placement) {
                return $placement->getCreated()->format('l');
            },
            $placements
        );

        $distributionWon = [];

        foreach ($cashBoxChanges as $nr => $cashBoxChange) {
            if ($nr === 0) {
                continue;
            }
            $accessNr = $nr - 1;

            $key = $placements[$accessNr]->isWon() ? 'won' : 'loose';
            if (array_key_exists($weekDays[$accessNr], $distributionWon)) {
                $distributionWon[$weekDays[$accessNr]][$key] = $distributionWon[$weekDays[$accessNr]][$key] + 1;
            } else {
                $distributionWon[$weekDays[$accessNr]] = ['won' => 0, 'loose' => 0];
                $distributionWon[$weekDays[$accessNr]][$key] = $distributionWon[$weekDays[$accessNr]][$key] + 1;
            }
        }

        return $distributionWon;
    }

    public function getStatistics(Simulator $simulator): array
    {
        $placements = $simulator->getTipicoPlacements()->toArray();
        if (count($placements) === 0) {
            return '';
        }
        $cashBoxChanges = $this->getCashBoxChangeArray($placements);

        $lowest = 100.0;
        $lowestNr = 1;
        $highest = 100.0;
        $highestNr = 1;

        foreach ($cashBoxChanges as $nr => $cashBoxChange) {
            if ($cashBoxChange < $lowest) {
                $lowest = $cashBoxChange;
                $lowestNr = $nr;
            }
            if ($cashBoxChange > $highest) {
                $highest = $cashBoxChange;
                $highestNr = $nr;
            }

        }

        $timeDistance = '-';

        $highestPlacements = $placements[$highestNr - 1]->getCreated();
        $current = $placements[array_key_last($cashBoxChanges) - 1]->getCreated();
        $interval = $highestPlacements->diff($current);
        if ($interval->d > 0) {
            $timeDistance .= $interval->d . ' days ';
        }
        if ($interval->h > 0) {
            $timeDistance .= $interval->h . ' hours ';
        }
        if ($interval->i > 0) {
            $timeDistance .= $interval->i . ' minutes ';
        }
        if ($interval->s > 0) {
            $timeDistance .= $interval->s . ' seconds ';
        }

        $distance = -1 * ($highest - $cashBoxChanges[array_key_last($cashBoxChanges)]);
        if ($timeDistance === '-') {
            $distance = '-';
        }

        $result = [
            'lowest' => $lowest,
            'highest' => $highest,
            'currentDistance' => $distance,
            'minusSince' => $timeDistance,
        ];

        return $result;
        $table = '<table>';
        foreach ($result as $key => $parameter) {
            $t = sprintf('<tr><td>%s</td><td>%s</td></tr>', $key, $parameter);
            $table = $table . $t;
        }
        $table = $table . '</table>';

        return $table;
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
                    'suggestedMax' => 8,
                ],
            ],
        ]);
        return $chart;
    }
}
