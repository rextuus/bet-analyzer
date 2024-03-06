<?php

namespace App\Twig\Components;

use App\Entity\TipicoPlacement;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Simulator
{
    public \App\Entity\Simulator $simulator;

    public function __construct(
        private readonly ChartBuilderInterface $chartBuilder
    )
    {
    }

    public function getDescription(): string
    {
        $strategy = $this->simulator->getStrategy();
        $parameters = json_decode($strategy->getParameters(), true);
        $table = '<table>';
        foreach ($parameters as $key => $parameter){
            $t = sprintf('<tr><td>%s</td><td>%s</td></tr>', $key, $parameter);
            $table = $table . $t;
        }
        $table = $table . '</table>';

        return $table;
    }

    public function getStatistics(): string
    {
        $placements = $this->simulator->getTipicoPlacements()->toArray();
        $cashBoxChanges = $this->getCashBoxChangeArray($placements);

        $lowest = 100.0;
        $lowestNr = 0;
        $highest = 100.0;
        $highestNr = 0;
        foreach ($cashBoxChanges as $nr => $cashBoxChange){
            if ($cashBoxChange < $lowest){
                $lowest = $cashBoxChange;
                $lowestNr = $nr;
            }
            if ($cashBoxChange > $highest){
                $highest = $cashBoxChange;
                $highestNr = $nr;
            }
        }

        $highestPlacements = $placements[$highestNr]->getCreated();
        $current = $placements[array_key_last($cashBoxChanges)-1]->getCreated();
        $interval = $highestPlacements->diff($current);

        $time_distance = '';

        if ($interval->d > 0) {
            $time_distance .= $interval->d . ' days ';
        }

        if ($interval->h > 0) {
            $time_distance .= $interval->h . ' hours ';
        }

        if ($interval->i > 0) {
            $time_distance .= $interval->i . ' minutes ';
        }

        if ($interval->s > 0) {
            $time_distance .= $interval->s . ' seconds ';
        }

        $result = [
            'lowest' => $lowest,
            'highest' => $highest,
            'currentDistance' => -1 * ($highest - $cashBoxChanges[array_key_last($cashBoxChanges)]),
            'minusSince' => $time_distance,
        ];

        $table = '<table>';
        foreach ($result as $key => $parameter){
            $t = sprintf('<tr><td>%s</td><td>%s</td></tr>', $key, $parameter);
            $table = $table . $t;
        }
        $table = $table . '</table>';

        return $table;
    }

    public function getBetOutcomeChart(): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);

        $placements = $this->simulator->getTipicoPlacements()->toArray();
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

    public function getCashBoxChart(bool $timeBased = false): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);

        $placements = $this->simulator->getTipicoPlacements()->toArray();
        $nr = array_keys($placements);
        $nr[] = count($nr);

        if ($timeBased){
            $nr = [$placements[array_key_first($placements)]->getCreated()->format('d/m H:i')];
            foreach ($placements as $placement){
                $nr[] = $placement->getCreated()->format('d/m H:i');
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
    private function getCashBoxChangeArray(array $placements): array
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
}
