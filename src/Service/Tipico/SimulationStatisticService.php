<?php
declare(strict_types=1);

namespace App\Service\Tipico;

use App\Entity\Simulator;
use App\Entity\TipicoBet;
use App\Entity\TipicoPlacement;
use App\Service\Evaluation\BetOn;
use App\Service\Tipico\SimulationProcessors\AbstractSimulationProcessor;
use App\Twig\Data\KeyValueListingContainer;
use Symfony\UX\Chartjs\Model\Chart;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class SimulationStatisticService
{
    public const KEY_WON = 'won';
    public const KEY_LOOSE = 'loose';
    public const KEY_WON_PERCENTAGE = 'won_percentage';
    public const KEY_LOOSE_PERCENTAGE = 'loose_percentage';
    public const KEY_DISTRIBUTION_PERCENTAGE = 'distribution_percentage';

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

    public function getValueToWinDistributionChart(Simulator $simulator): Chart
    {
        return $this->chartService->getValueToWinDistributionChart(
            $this->getValueDistributionStatisticForSearchBetOn($simulator)
        );
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

            $key = $placements[$accessNr]->isWon() ? self::KEY_WON : self::KEY_LOOSE;
            if (!array_key_exists($weekDays[$accessNr], $distributionWon)) {
                $distributionWon[$weekDays[$accessNr]] = [self::KEY_WON => 0, self::KEY_LOOSE => 0];
            }
            $distributionWon[$weekDays[$accessNr]][$key] = $distributionWon[$weekDays[$accessNr]][$key] + 1;
        }

        return $distributionWon;
    }

    public function getStatistics(Simulator $simulator): array
    {
        $placements = $simulator->getTipicoPlacements()->toArray();

        if ($placements === []){
            return [];
        }

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
            $distance = 0.0;
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

        $valueDistribution = $this->convertToContainer(
            $this->getValueDistributionStatisticForActuallyBetOn($simulator),
            count($placements)
        );
        $valueDistributionByBetOn = $this->convertToContainer(
            $this->getValueDistributionStatisticForSearchBetOn($simulator),
            count($placements)
        );

        return [
            'result' => $result,
            'dailyRatios' => $dailyRatios,
            'valueDistribution' => $valueDistribution,
            'valueDistributionByBetOn' => $valueDistributionByBetOn,
        ];
    }

    /**
     * distribute bets by the betOn parameter of the simulator => against_12_13_draw will deliver the odd home values between 1.2 and 1.3
     */
    private function getValueDistributionStatisticForSearchBetOn(Simulator $simulator): array
    {
        $placements = $simulator->getTipicoPlacements()->toArray();
        $parameter = json_decode($simulator->getStrategy()->getParameters(), true);
        $betOn = BetOn::tryFrom($parameter[AbstractSimulationProcessor::PARAMETER_BET_ON]);

        $distribution = [];
        foreach ($placements as $placement) {
            $fixture = $placement->getFixtures()->get(0);

            $key = (string)round($this->getOddByBetOn($fixture, $betOn), 2);
            $subKey = $placement->isWon() ? self::KEY_WON : self::KEY_LOOSE;
            if (!array_key_exists($key, $distribution)) {
                $distribution[$key] = [self::KEY_WON => 0, self::KEY_LOOSE => 0];
            }
            $distribution[$key][$subKey] = $distribution[$key][$subKey] + 1;
        }

        ksort($distribution);

        foreach ($distribution as $betValue => $step) {
            $total = $step[self::KEY_WON] + $step[self::KEY_LOOSE];
            $wonPercentage = $this->calculatePercentage($step[self::KEY_WON], $total);
            $loosePercentage = $this->calculatePercentage($step[self::KEY_LOOSE], $total);
            $distributionPercentage = $this->calculatePercentage($total, $simulator->getTipicoPlacements()->count());

            $step[self::KEY_WON_PERCENTAGE] = $wonPercentage;
            $step[self::KEY_LOOSE_PERCENTAGE] = $loosePercentage;
            $step[self::KEY_DISTRIBUTION_PERCENTAGE] = $distributionPercentage;
            $distribution[$betValue]  = $step;
        }

        return $distribution;
    }

    private function getOddByBetOn(TipicoBet $bet, BetOn $betOn): float
    {
        match ($betOn) {
            BetOn::HOME => $value = $bet->getOddHome(),
            BetOn::DRAW => $value = $bet->getOddDraw(),
            BetOn::AWAY => $value = $bet->getOddAway(),
        };
        return $value;
    }

    /**
     * distribute bets by the actually odd we bet on => against_12_13_draw will deliver the odd draw values
     */
    private function getValueDistributionStatisticForActuallyBetOn(Simulator $simulator): array
    {
        $placements = $simulator->getTipicoPlacements()->toArray();

        $distribution = [];
        foreach ($placements as $placement) {
            $key = (string)round($placement->getValue(), 2);
            $subKey = $placement->isWon() ? self::KEY_WON : self::KEY_LOOSE;
            if (!array_key_exists($key, $distribution)) {
                $distribution[$key] = [self::KEY_WON => 0, self::KEY_LOOSE => 0];
            }
            $distribution[$key][$subKey] = $distribution[$key][$subKey] + 1;
        }
        ksort($distribution);

        return $distribution;
    }

    private function convertToContainer(array $distribution, int $totalPlacementAmount): KeyValueListingContainer
    {
        $container = new KeyValueListingContainer();
        foreach ($distribution as $betValue => $step) {
            $total = $step[self::KEY_WON] + $step[self::KEY_LOOSE];
            $wonPercentage = $this->calculatePercentage($step[self::KEY_WON], $total);
            $distributionPercentage = $this->calculatePercentage($total, $totalPlacementAmount);

            $value = sprintf(
                '%d | %d (%.2f%%)',
                $step[self::KEY_WON],
                $step[self::KEY_LOOSE],
                $wonPercentage
            );

            $key = sprintf(
                '%.2f (%.2f%%)',
                $betValue,
                $distributionPercentage
            );

            $container->addEntry($key, $value);
        }

        return $container;
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