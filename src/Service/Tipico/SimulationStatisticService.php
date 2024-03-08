<?php
declare(strict_types=1);

namespace App\Service\Tipico;

use App\Entity\Simulator;
use App\Entity\TipicoPlacement;
use App\Twig\Data\KeyValueListingContainer;
use Symfony\UX\Chartjs\Model\Chart;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class SimulationStatisticService
{
    public function __construct(
        private readonly SimulationChartService $chartService
    )
    {
    }

    public function getCashBoxChart(Simulator $simulator, bool $timeBased = true): Chart
    {
        $placements = $simulator->getTipicoPlacements()->toArray();
        return $this->chartService->getCashBoxChart(
            $simulator,
            $this->getCashBoxChangeArray($placements),
            $timeBased
        );
    }

    public function getDailyDistributionChart(Simulator $simulator): Chart
    {
        return $this->chartService->getDailyDistributionChart($this->getWeekDayStatistics($simulator));
    }

    public function getBetOutcomeChart(Simulator $simulator): Chart
    {
        return $this->chartService->getBetOutcomeChart($simulator);
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
            if (!array_key_exists($weekDays[$accessNr], $distributionWon)) {
                $distributionWon[$weekDays[$accessNr]] = ['won' => 0, 'loose' => 0];
            }
            $distributionWon[$weekDays[$accessNr]][$key] = $distributionWon[$weekDays[$accessNr]][$key] + 1;
        }

        return $distributionWon;
    }

    public function getStatistics(Simulator $simulator): array
    {
        $placements = $simulator->getTipicoPlacements()->toArray();

        $positiveChanges = [];
        $values = [];
        $totalWon = 0;
        $totalLoose = 0;
        foreach ($placements as $placement) {
            if ($placement->isWon()) {
                $positiveChanges[] = ($placement->getInput() * $placement->getValue()) - $placement->getInput();
                $totalWon++;
            } else {
                $totalLoose++;
            }
            $values[] = $placement->getValue();
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

        $dailyRatios = $this->getWeekDayStatisticContainer($simulator);

        $wonPercentage = $this->calculatePercentage($totalWon, count($placements));
        $loosePercentage = $this->calculatePercentage($totalLoose, count($placements));

        $result = new KeyValueListingContainer();
        $result->addEntry('bets', (string)count($placements))
            ->addEntry('averageValue', (string)round($this->calculateAverage($values), 2))
            ->addEntry('won', sprintf('%d (%.2f%%)', $totalWon, $wonPercentage))
            ->addEntry('loose', sprintf('%d (%.2f%%)', $totalLoose, $loosePercentage))
            ->addEntry('lowest', (string)round($lowest, 2))
            ->addEntry('highest', (string)round($highest, 2))
            ->addEntry('averageWin', (string)round($this->calculateAverage($positiveChanges), 2))
            ->addEntry('currentDistance', (string)round($distance, 2))
            ->addEntry('minusSince', (string)$timeDistance);

        return [
            'result' => $result,
            'dailyRatios' => $dailyRatios
        ];
    }

    private function getWeekDayStatisticContainer(Simulator $simulator): KeyValueListingContainer
    {
        $weekdayStatistic = $this->getWeekDayStatistics($simulator);
        $totalWon = 0;
        $totalLoose = 0;

        $dailyRatios = new KeyValueListingContainer();
        foreach ($weekdayStatistic as $dayName => $day) {
            $won = $day['won'];
            $loose = $day['loose'];
            $total = $won + $loose;

            $totalWon = $totalWon + $won;
            $totalLoose = $totalLoose + $loose;

            $dailyRatios->addEntry($dayName, sprintf("%d (%.2f%%)", $total, ($won / $total) * 100));
        }

        return $dailyRatios;
    }

    private function calculatePercentage(float $value, float $total): float
    {
        return round($value / $total * 100, 2);
    }

    private function calculateAverage(array $values): float
    {
        $cleanValues = array_filter($values);
        if (count($cleanValues)) {
            return array_sum($cleanValues) / count($cleanValues);
        }

        return 0.0;
    }
}
